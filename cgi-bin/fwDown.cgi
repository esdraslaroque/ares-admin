#!/bin/bash
#-------------------------------------------------------------------------------------------------------------
# Descricao:
# 	Programa CGI utilizado pelo Autenticador 2.0 do ARES para baixar regras iptables
# 	via sudo, utilizando banco de dados SQLite3.
#
# Autor:
#	11/12/2015 :: Esdras La-Roque <esdras.laroque@sefa.pa.gov.br>
#
# Parametros esperados:
#	conexao_id => Codigo da conexao criada na tabela "conexao", passado via metodo GET
#	usuario_id => Codigo do usuario, passado via metodo GET
#
# Retorno:
#	NULL
#
# Alteracoes:
#
#-------------------------------------------------------------------------------------------------------------
# TODO:
#
# 1) Gerar logs no SO com logger
#

# Extremamente necessario para executar como CGI do Apache
echo "content-type: application/json"
echo

CONEXAO_ID=`echo "$QUERY_STRING" | sed -n 's/^.*conexao_id=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`
USUARIO_ID=`echo "$QUERY_STRING" | sed -n 's/^.*usuario_id=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`
ATIVO=`echo "$QUERY_STRING" | sed -n 's/^.*ativo=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`
IPTABLES=/sbin/iptables
DESTINO=""

IP_CLIENTE=`sqlite3 /var/db/ares/ares.db \
	    "SELECT ip_cliente FROM conexao WHERE id = $CONEXAO_ID;"`

sqlite3 /var/db/ares/ares.db \
   "SELECT regra.destino, regra.proto, regra.servico, regra.acao FROM regra \
    JOIN permissao ON permissao.regra_id = regra.id \
    JOIN firewall ON firewall.regra_id = regra.id \
    WHERE firewall.conexao_id = $CONEXAO_ID AND permissao.usuario_id = $USUARIO_ID AND ativo = $ATIVO;" | while read campo; 
    do
	DESTINO=`echo $campo | awk -F"|" '{print $1}'`
	PROTO=`echo $campo | awk -F"|" '{print $2}'`
	PORTA=`echo $campo | awk -F"|" '{print $3}'`
	ACAO=`echo $campo | awk -F"|" '{print $4}'`
	if [ $ACAO == 1 ]; then
		ACAO="ACCEPT"
	else
		ACAO="REJECT"
	fi
	sudo $IPTABLES -D FORWARD -s $IP_CLIENTE -d $DESTINO -p $PROTO --dport $PORTA -j $ACAO
    done

exit 
