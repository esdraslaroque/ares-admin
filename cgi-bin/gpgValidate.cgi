#!/bin/bash
#-------------------------------------------------------------------------------------------------------------
# Descricao:
# 	Programa CGI utilizado pelo Autenticador 2.0 do ARES para validar a chave GnuPG
# 	via chamada HTTP, utilizando banco de dados SQLite3.
#
# Autor:
#	03/12/2015 :: Esdras La-Roque <esdras.laroque@sefa.pa.gov.br>
#
# Parametros esperados:
#	keyname => Nome do arquivo de chave publica upado para /tmp, passado via metodo GET
#
# Retorno:
#	Retorna um codigo de status e informacoes de erro ou dados validados da chave em JSON
#
# Alteracoes:
#
#-------------------------------------------------------------------------------------------------------------
# TODO:
#
# 1) Gerar logs no SO com logger
#

# Extremamente necessario para executar como CGI do Apache
echo "content-type: application/json; charset=UTF-8"
echo

# Nome do arquivo de chave que foi upado para /tmp/$keyname e passado como parametro via GET
KEY=`echo "$QUERY_STRING" | sed -n 's/^.*keyname=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`

# Recuperando login conectado no PPP, atraves do Endereco IP, no banco de dados
USER=`sqlite3 /var/db/ares/ares.db \
	"SELECT login FROM pessoa \
	 JOIN conexao ON pessoa.id = conexao.usuario_id \
	 WHERE conexao.ip_cliente = '$REMOTE_ADDR' AND conexao.fim IS NULL \
	 ORDER BY conexao.inicio DESC LIMIT 1;"`

GPG_ID=`gpg --homedir /home/ARES/$USER/.gnupg/ --with-colons --list-keys \
	--no-default-keyring --keyring /tmp/$KEY | egrep "^pub.*" | cut -d: -f5`

# Recuperar PID da conexao para logging
PID=`sqlite3 /var/db/ares/ares.db \
        "SELECT pid FROM conexao \
         WHERE ip_cliente = '$REMOTE_ADDR' AND fim IS NULL \
         ORDER BY inicio DESC LIMIT 1;"`

logger -t ares[$PID] -p daemon.info "INFO - Chave ARES apresentada: $KEY ($GPG_ID)"

function print_cod {
	printf '{"cod":%d, "msg":"%s"}\n' $1 "$2"
}

function delete_key {
	rm -f /tmp/$KEY
}

# Verificar se o nome da chave foi passado corretamente, via GET
if [ ! "$KEY" ]; then
	print_cod 1 "A chamada ao validador de chave não foi feita corretamente!";
	exit
fi

# Verificar se o arquivo upado existe, pode ser lido e nao esta vazio
if [ ! -f "/tmp/$KEY" ]; then
	print_cod 101 "Problema ao verificar chave no servidor"
	logger -t ares[$PID] -p daemon.err "ERROR - Problema ao verificar chave no servidor (Erro: 101)"
	if [ ! -d "$KEY" ]; then 
		delete_key
	fi
	exit
elif [ ! -r "/tmp/$KEY" ]; then
        print_cod 102 "Problema ao verificar chave no servidor"
	logger -t ares[$PID] -p daemon.err "ERROR - Problema ao verificar chave no servidor (Erro: 102)"
 	if [ ! -d "$KEY" ]; then 
		delete_key
	fi      
        exit
elif [ ! -s "/tmp/$KEY" ]; then
        print_cod 103 "Problema ao verificar chave no servidor"
	logger -t ares[$PID] -p daemon.err "ERROR - Problema ao verificar chave no servidor (Erro: 103)"
 	if [ ! -d "$KEY" ]; then 
		delete_key
	fi
        exit
fi

# Checando validade do arquivo de chave informado no Autenticador
if [ "`file -b /tmp/$KEY`" != "GPG key public ring" ]; then
	print_cod 110 "Arquivo de chave inválido"
	logger -t ares[$PID] -p daemon.err "ERROR - Arquivo de chave inválido (Erro: 110)"
	delete_key
	exit
fi

# Verificar se a conexao ja esta autenticada
AUTHD=`sqlite3 /var/db/ares/ares.db \
	"SELECT autenticado FROM conexao \
	 WHERE ip_cliente = '$REMOTE_ADDR' AND fim IS NULL \
	 ORDER BY inicio DESC LIMIT 1;"`

if [ "$AUTHD" == 1 ]; then
	print_cod 120 "Conexão já autenticada!"
	logger -t ares[$PID] -p daemon.warn "WARN - Conexão já autenticada (Erro: 120)"
	delete_key
	exit
fi

if [ ! "$USER" ]; then
	print_cod 130 "Problema na conexão. Reconecte seu ARES!"
	delete_key
	exit
fi

# Verificando se o usuario esta ativo
ATIVO=`sqlite3 /var/db/ares/ares.db \
	"SELECT ativo FROM usuario \
	 JOIN pessoa ON pessoa.id = usuario.id \
	 WHERE pessoa.login = '$USER';"`

if [ "$ATIVO" == 0 ]; then
        print_cod 140 "Usuário desativado no sistema ARES. Contate o SACS para mais informações."
	logger -t ares[$PID] -p daemon.warn "WARN - Usuário desativado no sistema ARES (Erro: 140)"
        delete_key
        exit
fi

GPG_LOGIN=`gpg --homedir /home/ARES/$USER/.gnupg/ --with-colons --list-keys \
	   --no-default-keyring --keyring /tmp/$KEY | egrep "^uid.*" | cut -d% -f2`

# Verificar se o dono da chave e igual ao usuario conectado
if [ "$GPG_LOGIN" != "$USER" ]; then
	print_cod 150 "A chave não pertence ao usuário conectado"
	logger -t ares[$PID] -p daemon.err "ERROR - A chave não pertence ao usuário conectado (Erro: 150)"
	delete_key
	exit
fi

ID_KEY=`sqlite3 /var/db/ares/ares.db \
	"SELECT id_key FROM usuario \
	 JOIN pessoa ON pessoa.id = usuario.id \
	 WHERE login = '$USER';"`

if [ "$GPG_ID" != "$ID_KEY" ]; then
	print_cod 160 "A chave não é a mais recente"
	logger -t ares[$PID] -p daemon.err "ERROR - A chave não é a mais recente (Erro: 160)"
	delete_key
	exit
fi

# Validando o status da chave
STATUS=`gpg --homedir /home/ARES/$USER/.gnupg/ --with-colons --list-keys \
	--no-default-keyring --keyring /tmp/$KEY | egrep "^pub.*" | cut -d: -f2`

case "$STATUS" in
	-|q|f|u)
		CONEXAO_ID=`sqlite3 /var/db/ares/ares.db \
			    "SELECT conexao.id FROM conexao \
			     JOIN pessoa ON pessoa.id = usuario.id \
			     JOIN usuario ON conexao.usuario_id = usuario.id \
			     WHERE pessoa.login = '$USER' \
			     AND ip_cliente = '$REMOTE_ADDR' AND conexao.fim IS NULL \
			     ORDER BY conexao.id DESC LIMIT 1;"`

		USUARIO_ID=`sqlite3 /var/db/ares/ares.db \
			    "SELECT id FROM pessoa WHERE login = '$USER';"`

	        EXPIRE=`gpg --homedir /home/ARES/$USER/.gnupg --list-keys \
        	        --keyring /home/ARES/$USER/$USER.pub 2> /dev/null | grep ^pub | cut -d" " -f7 | cut -d] -f1`

		# Imprimindo dados no formato JSON
		printf '{"cod":%d, "login":"%s", "usuario_id":%d, "ip_cliente":"%s", "gpgid":"%s", "conexao_id":%d, "expire":"%s"}\n' \
			0 "$USER" $USUARIO_ID "$REMOTE_ADDR" "$GPG_ID" "$CONEXAO_ID" "$EXPIRE"
		delete_key
		exit
	;;
	*)
		print_cod 170 "Chave inválida ou expirada!"
		logger -t ares[$PID] -p daemon.err "ERROR - Chave inválida ou expirada (Erro: 170)"
		delete_key
		exit
	;;
esac

exit 0

