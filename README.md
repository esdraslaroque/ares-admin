# Repositório do Gerenciador Web ARES 

## Tecnologias utilizadas
Este projeto foi desenvolvido utilizando as seguintes ferramentas:

* Apache HTTPd 2.2
* PHP 5.3
* CodeIgniter 3.0
* AngularJS 1.5
* SQLite 3.6

### API Python Zabbix
A funcionalidade de criacao do host no Zabbix exigea instalacao do modulo
zabbix-api do Python na maquina que esta executando o playboook (desktop do admin):

```
$ sudo yum install python-pip
$ pip install zabbix-api
```

### Acesso ao repositorio RPM Zabbix
Para instalacao dos agentes e necessario adicionar o novo cliente ao grupo "Clientes Zabbix" 
no Palo Alto para que ele tenha cesso ao repo.zabbix.com para baixar os pacotes RPM de acordo
com a versao da distribuicao.

### Monitoramento do MySQL/MariaDB pelo Zabbix
Caso o cliente a ser monitorado **NAO** possua base MySQL/MariaDB que precise ser monitorada é preciso
utilizar o parametro `--skip-tags mysql`. A nao utilizacao desse parametro fara com que um usuario 
"zabbix" sera criado localmente com privilegios de "USAGE" em todas bases presentes no host.

## Utilizacao
A conexao sera realizada com o usuario do dominio Linux e com o parametro `-b`, as tasks
serao executadas com 'sudo' para escalada de privilegios.

```
$ sudo echo "cliente.sefa.pa.ipa" >> /etc/ansible/hosts
$ ansible-playbook -u rodrigo.brasil -b -e hosts="cliente.sefa.pa.ipa" --skip-tags myslq main.yml
```

