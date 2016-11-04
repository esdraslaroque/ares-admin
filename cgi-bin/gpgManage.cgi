#!/bin/bash
#-------------------------------------------------------------------------------------------------------------
# Descricao:
# 	Programa CGI utilizado para gerenciar chaves publicas GnuPG:
#	- Cria nova
#	- Renova	
#	- Consulta
#
# Autor:
#	26/02/2016 :: Esdras La-Roque <esdras.laroque@sefa.pa.gov.br>
#
# Parametros esperados:
#	username => Login de rede do usuario
#	op => Operacao desejada
#	valid => Validade da chave (somente para criacao ou renovacao)
#
# Retorno:
#	Retorna um codigo de status e informacoes de erro ou dados validados da chave em JSON
#
# Alteracoes:
#
#-------------------------------------------------------------------------------------------------------------
# TODO:
#

# Recuperando IPs das interfaces de rede
ETH0=`ip addr show eth0 | grep "inet " | cut -d" " -f6 | cut -d\/ -f1`
ETH1=`ip addr show eth1 | grep "inet " | cut -d" " -f6 | cut -d\/ -f1`

# Restricao de acesso ao modulo 
if [ "$REMOTE_ADDR" != "$ETH0" ]; then
	if [ "$REMOTE_ADDR" != "$ETH1" ]; then
		if [ "$REMOTE_ADDR" != "127.0.0.1" ]; then
			echo "Content-Type: text/html"
			echo "Status: 403 Forbidden"
			echo
			echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\"> 
				<html><head> 
				<title>403 Forbidden</title>
				</head><body>
				<h1>Forbidden</h1>
				<p>You don't have permission to access $SCRIPT_NAME
				on this server.</p>
				</body></html>"
			exit
		fi
	fi
else
	echo "Content-Type: application/json; charset=UTF-8"
fi
echo

# Parametros passados via GET
USERNAME=`echo "$QUERY_STRING" | sed -n 's/^.*username=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`
OPER=`echo "$QUERY_STRING" | sed -n 's/^.*op=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`

#``````````````````````````
# Functions
#,,,,,,,,,,,,,,,,,,,,,,,,,,

function print_cod {
	printf '{"cod":%d, "msg":"%s"}\n' $1 "$2"
}

function vrfy_key {
	if [ ! -d "/home/ARES/$1" ]; then
		print_cod 1 "Usuário sem acesso ARES"
		exit
	elif [ ! -d "/home/ARES/$1/.gnupg" ]; then
		print_cod 1 "Repositório privado de chave não encontrado para $1"
		exit
	fi
}

function vrfy_id {
	/usr/bin/getent passwd $1 &> /dev/null

	if [ "$?" -gt "0" ]; then
		print_cod 1 "Usuário de rede <b>$1</b> inexistente."
		exit
	fi

	SQL=`sqlite3 /var/db/ares/ares.db \
        	"SELECT ativo FROM usuario \
		 JOIN pessoa ON usuario.id = pessoa.id \
		 WHERE login = '$1' LIMIT 1;"`

	[[ ! "$SQL" ]] && { print_cod 1 "Usuário $1 não possui cadastro no sistema ARES."; exit; }
	[[ "$SQL" == 0 ]] && { print_cod 1 "Usuário $1 desativado no sistema ARES."; exit; }
}

function vrfy_valid {
	ISNUMER='^[0-9]+$'

	if [ "$1" == "" ]; then
		print_cod 1 "Informe a validade da chave."
		exit
	elif ! [[ $1 =~ $ISNUMBER ]]; then
		print_cod 1 "A validade da chave é incompatível."
		exit
	elif [ $1 -gt 180 ]; then
		print_cod 1 "Validade acima do limite (6 meses)."
		exit
	fi
}

function query_key {
	USER=$1
	vrfy_key $USER	

	ID_KEY=`gpg --homedir /home/ARES/$USER/.gnupg --with-colons --list-keys \
		--keyring /home/ARES/$USER/$USER.pub 2> /dev/null | egrep "^pub.*" | cut -d: -f5`

	CREATE=`gpg --homedir /home/ARES/$USER/.gnupg --list-keys \
		--keyring /home/ARES/$USER/$USER.pub 2> /dev/null | grep ^pub | cut -d" " -f5`

	EXPIRE=`gpg --homedir /home/ARES/$USER/.gnupg --list-keys \
		--keyring /home/ARES/$USER/$USER.pub 2> /dev/null | grep ^pub | cut -d" " -f7 | cut -d] -f1`

	printf '{"cod":%d, "id_key":"%s", "criada":"%s", "expira":"%s"}\n' 0 "$ID_KEY" "$CREATE" "$EXPIRE"
}

function gen_key {
	# $1 - USER  ::  $2 - VALID
	SQL=`sqlite3 /var/db/ares/ares.db \
        	"SELECT nome, email FROM pessoa WHERE login = '$1' LIMIT 1;"`
	NAME=`echo $SQL | cut -d\| -f1`
	EMAIL=`echo $SQL | cut -d\| -f2`

	cat <<EOF | gpg --homedir /home/ARES/$1/.gnupg --batch --gen-key
	     %echo Generating a standard key for $1
	     Key-Type: RSA
	     Key-Length: 2048
	     Name-Real: $NAME 
	     Name-Comment: VPN key for %$1%
	     Name-Email: $EMAIL
	     Expire-Date: $2
	     %pubring /home/ARES/$1/$1.pub
	     %secring /home/ARES/$1/$1.sec
	     %commit
	     %echo Key for $1 done
EOF
	GPG_ID=`gpg --homedir /home/ARES/$1/.gnupg/ --with-colons --list-keys \
        	--no-default-keyring --keyring /home/ARES/$1/$1.pub | egrep "^pub.*" | cut -d: -f5`

        CREATE=`gpg --homedir /home/ARES/$1/.gnupg --list-keys \
                --keyring /home/ARES/$1/$1.pub 2> /dev/null | grep ^pub | cut -d" " -f5`

        EXPIRE=`gpg --homedir /home/ARES/$1/.gnupg --list-keys \
                --keyring /home/ARES/$1/$1.pub 2> /dev/null | grep ^pub | cut -d" " -f7 | cut -d] -f1`

	sqlite3 /var/db/ares/ares.db \
		"UPDATE usuario SET id_key = '$GPG_ID', expedida = '$CREATE', validade = '$EXPIRE' \
	 	 WHERE id = (SELECT id FROM pessoa WHERE login = '$1');"

	[[ -f "/home/ARES/$1/$1.pub" ]] && cp /home/ARES/$1/$1.pub /home/ARES/$1/public_html/
}

function renew_key {
	USER=$1
	VALID=`echo "$QUERY_STRING" | sed -n 's/^.*valid=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`

	vrfy_valid $VALID
	vrfy_id $USER
	vrfy_key $USER

	[[ -f "/home/ARES/$USER/$USER.pub" ]] && rm -f /home/ARES/$USER/$USER.pub
	[[ -f "/home/ARES/$USER/$USER.sec" ]] && rm -f /home/ARES/$USER/$USER.sec
	[[ -f "/home/ARES/$USER/public_html/$USER.pub" ]] && rm -f /home/ARES/$USER/public_html/$USER.pub

	gen_key "$USER" $VALID
	query_key $USER
}

function new_key {
        USER=$1
        VALID=`echo "$QUERY_STRING" | sed -n 's/^.*valid=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`

        vrfy_id $USER
        vrfy_valid $VALID

	[[ -d "/home/ARES/$1" ]] && { print_cod 1 "Usuário $USER já possui acesso ARES"; exit; }

	mkdir -p /home/ARES/$USER/.gnupg
	[[ $? != 0 ]] && { print_cod 1 "Não foi possível criar chave. (Permissão negada)"; exit; }

	mkdir -p /home/ARES/$USER/public_html
	chmod u+rwxs /home/ARES/$USER/.gnupg

	echo "Require user $USER" > /home/ARES/$USER/public_html/.htaccess

	[[ ! -r "/home/ARES/$USER/public_html/.htaccess" ]] && { print_cod 1 "Proteção da area de download para $USER falhou"; exit; }

	gen_key "$USER" $VALID
	query_key $USER
}

#``````````````````````````
# Main
#,,,,,,,,,,,,,,,,,,,,,,,,,,

if [ "$OPER" == "query" ]; then

	query_key $USERNAME

elif [ "$OPER" == "new" ]; then

	new_key $USERNAME	

elif [ "$OPER" == "renew" ]; then

	renew_key $USERNAME

else
	print_cod 1 "Operacão inválida!"
fi

exit 0
