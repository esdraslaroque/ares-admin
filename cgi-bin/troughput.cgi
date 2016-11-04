#!/bin/bash
#-------------------------------------------------------------------------------------------------------------
# Descricao:
# 	Programa CGI utilizado para coletar informações de tráfego de rede do servidor 
#
# Autor:
#	31/05/2016 :: Esdras La-Roque <esdras.laroque@sefa.pa.gov.br>
#
# Retorno:
#	Retorna um objeto JSON com 12 linhas de informações de dados recebidos e enviados
#	com suas respectivas timestamp
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

echo "["
awk -F"," 'NF && $1!~/^"/ {print " {\"in\":" $1",\"out\":"$2",\"date\":\""$3"\"},"}' /var/log/ares_traffic | tail -n12
echo " {\"in\":null,\"out\":null,\"date\": null}"
echo "]"

exit 0
