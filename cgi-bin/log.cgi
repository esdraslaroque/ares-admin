#!/bin/bash
#-------------------------------------------------------------------------------------------------------------
# Descricao:
# 	Programa CGI utilizado para coletar logs do serviço ARES no arquivo de log: /var/log/ares.log
#
# Autor:
#	31/05/2016 :: Esdras La-Roque <esdras.laroque@sefa.pa.gov.br>
#
# Retorno:
#	Retorna um objeto JSON com as últimas 500 linhas do final para o fim
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
awk -F" " '{ \
		gsub(/\"/, "", $0); \
		gsub(/\\/, "|", $0); \
		gsub(/'\''/, "", $0); \
		split(substr($0,27), a, "]:"); \
		split(a[1], b, "["); \
		print " {\"date\": \""substr($0,0,15)"\", \"service\": \""b[1]"\", \"pid\": \""b[2]"\", \"text\": \""a[2]"\"},"}' /var/log/ares.log | tail -n 500 | tac

echo " {\"date\":null,\"service\":null,\"pid\":null,\"text\":null}"
echo "]"

exit 0
