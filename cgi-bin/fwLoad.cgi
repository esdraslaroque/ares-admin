#!/bin/bash
#-------------------------------------------------------------------------------------------------------------
# Descricao:
# 	Programa CGI utilizado pelo Autenticador 2.0 do ARES para carregar regras iptables
# 	via sudo, utilizando banco de dados SQLite3.
#
# Autor:
#	11/12/2015 :: Esdras La-Roque <esdras.laroque@sefa.pa.gov.br>
#
# Parametros esperados:
#	conexao_id => Codigo da conexao criada na tabela "conexao", passado via metodo GET
#
# Retorno:
#	Retorna um codigo de status e informacoes de erro ou dados das permissoes em JSON
#
# Alteracoes:
#
#-------------------------------------------------------------------------------------------------------------
# TODO:
#

# Extremamente necessario para executar como CGI do Apache
echo "content-type: application/json; charset=UTF-8"
echo

CONEXAO_ID=`echo "$QUERY_STRING" | sed -n 's/^.*conexao_id=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`
USUARIO_ID=`echo "$QUERY_STRING" | sed -n 's/^.*usuario_id=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`
IPTABLES=/sbin/iptables
DESTINO=""

# Verificar se a conexao ja esta autenticada
AUTHD=`sqlite3 /var/db/ares/ares.db \
        "SELECT autenticado FROM conexao \
         WHERE ip_cliente = '$REMOTE_ADDR' AND fim IS NULL \
         ORDER BY inicio DESC LIMIT 1;"`

# Busca PID da conexao para logging
PID=`sqlite3 /var/db/ares/ares.db \
        "SELECT pid FROM conexao \
         WHERE id = $CONEXAO_ID;"`

if [ "$AUTHD" == 1 ]; then
	logger -t ares[$PID] -p daemon.warn "WARN - Conexão já autenticada"
        printf '{"cod":%d, "msg":"%s"}\n' 1 "Conexão já autenticada!"
        exit
fi

sqlite3 /var/db/ares/ares.db \
   "SELECT regra.destino, regra.proto, regra.servico, regra.acao, permissao.id, regra.descricao FROM regra \
    JOIN permissao ON permissao.regra_id = regra.id \
    JOIN firewall ON firewall.regra_id = regra.id \
    WHERE firewall.conexao_id = $CONEXAO_ID AND permissao.usuario_id = $USUARIO_ID AND ativo = 0 ;" | while read campo; do
	DESTINO=`echo $campo | awk -F"|" '{print $1}'`
	PROTO=`echo $campo | awk -F"|" '{print $2}'`
	PORTA=`echo $campo | awk -F"|" '{print $3}'`
	ACAO=`echo $campo | awk -F"|" '{print $4}'`
	PERM_ID=`echo $campo | awk -F"|" '{print $5}'`
	PERM_DESC=`echo $campo | awk -F"|" '{print $6}'`
	if [ $ACAO == 1 ]; then
		ACAO="ACCEPT"
	else
		ACAO="REJECT"
	fi
	sudo $IPTABLES -A FORWARD -s $REMOTE_ADDR -d $DESTINO -p $PROTO --dport $PORTA -j $ACAO
done

logger -t ares[$PID] -p daemon.info "INFO - Regras de firewall ativadas"

sqlite3 /var/db/ares/ares.db \
	"UPDATE firewall \
	 SET ativo = 1 \
	 WHERE conexao_id = $CONEXAO_ID AND ativo = 0;"

# Setando conexao como autenticada
sqlite3 /var/db/ares/ares.db \
	"UPDATE conexao \
	 SET autenticado = 1 \
	 WHERE id = $CONEXAO_ID;"

AUTHD=`sqlite3 /var/db/ares/ares.db \
        "SELECT autenticado FROM conexao \
         WHERE ip_cliente = '$REMOTE_ADDR' AND fim IS NULL \
         ORDER BY inicio DESC LIMIT 1;"`

if [ "$AUTHD" == 1 ]; then
	logger -t ares[$PID] -p daemon.info "INFO - Autenticação da chave ARES realizada com sucesso"
	printf '{"cod":%d, "descricao":"%s"}\n' 0 "auth ok"
else
	logger -t ares[$PID] -p daemon.warn "WARN - Autenticação da chave ARES falhou na aplicação fwLoad"
        printf '{"cod":%d, "msg":"%s"}\n' 1 "Falha na autenticação!"
fi

exit 
