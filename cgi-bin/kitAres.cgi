#!/bin/bash
#-------------------------------------------------------------------------------------------------------------
# Descricao:
# 	Programa CGI utilizado para gerar os arquivos de instalação do Kit ARES
# Autor:
#	13/04/2016 :: Esdras La-Roque <esdras.laroque@sefa.pa.gov.br>
#
# Parametros esperados:
#	username => Login de rede do usuario
#
# Retorno:
#	Retorna um codigo de status e informacoes de erro ou sucesso
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
HOME=`echo "$QUERY_STRING" | sed -n 's/^.*home=\([^&]*\).*$/\1/p' | sed "s/%20/ /g"`

#``````````````````````````
# Functions
#,,,,,,,,,,,,,,,,,,,,,,,,,,

function print_cod {
	printf '{"cod":%d, "msg":"%s"}\n' $1 "$2"
}

function vrfy_home {
	if [ ! -d "$2" ]; then
		print_cod 1 "Usuário sem acesso ARES"
		exit
	elif [ ! -d "$2/public_html" ]; then
		print_cod 1 "Repositório de download não encontrado para $1"
		exit
	elif [ -f "$2/public_html/ares-$1.zip" ]; then
		print_cod 1 "O usuário $1 já possui um kit em seu repositório de download"
                exit
	fi
}

function vrfy_id {
	/usr/bin/getent passwd $1 &> /dev/null

	if [ "$?" -gt "0" ]; then
		print_cod 1 "Usuário de rede $1 inexistente."
		exit
	fi

	SQL=`sqlite3 /var/db/ares/ares.db \
        	"SELECT ativo FROM usuario \
		 JOIN pessoa ON usuario.id = pessoa.id \
		 WHERE login = '$1' LIMIT 1;"`

	[[ ! "$SQL" ]] && { print_cod 1 "Usuário $1 não possui cadastro no sistema ARES."; exit; }
}

function gen_kitares {
	vrfy_id "$1"
	vrfy_home "$1" "$2"

	mkdir $2/ares-$1
	cp -r /var/lib/ares/template/* $2/ares-$1
	cp $2/$1.pub $2/ares-$1
	cd $2
	/usr/bin/zip -r ares-$1.zip ares-$1 &> /dev/null
	mv ares-$1.zip $2/public_html
	rm -rf $2/ares-$1

	print_cod 0 "Kit ARES do usuário $1 gerado com sucesso."
}

#``````````````````````````
# Main
#,,,,,,,,,,,,,,,,,,,,,,,,,,

gen_kitares $USERNAME $HOME

exit 0
