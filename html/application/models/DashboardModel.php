<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class DashboardModel extends CI_Model {
        public function __construct() {
        $this->load->database();
    }

//SELECT (SELECT COUNT (ativo) FROM usuario WHERE validade > date('now') AND ativo = 1) AS Ativo_Ne, (SELECT COUNT (ativo) FROM usuario WHERE validade <= date('now') AND ativo = 1) AS Ativo_E, COUNT (ativo) AS Inativo FROM usuario WHERE ativo = 0;
//#######################################################################################
         public function get_user_ativos_geral(){
            $this->db->select('(SELECT COUNT (ativo) FROM usuario WHERE validade > date("now") AND ativo = 1) AS Ativo, (SELECT COUNT (ativo) FROM usuario WHERE validade <= date("now") AND ativo = 1) AS Expirados, (SELECT COUNT (ativo) FROM usuario WHERE  ativo = 0) AS Inativo');
            $this->db->from('usuario');
            $query = $this->db->get();
            return $query->row_array();
    }

//#######################################################################################
//quantidade de usuarios com 15 dias para expirar
//SELECT COUNT(ativo) AS ativo FROM usuario WHERE validade > date() AND validade <= date('now', '+15days')  ;
//
         public function get_qtd_15day(){
            $this->db->select('COUNT (ativo) AS ativo');
            $this->db->from('usuario');
            $where = "validade > date() AND validade <= date('now', '+15days')";
            $this->db->where($where);
           $query = $this->db->get();
            return $query->row()->ativo;

    }

//#######################################################################################
//usuarios conectaram  no mês de abril
//SELECT conexao.usuario_id, pessoa.nome, conexao.inicio FROM conexao JOIN usuario ON conexao.usuario_id = usuario.id JOIN pessoa ON usuario.pessoa_id = pessoa.id WHERE conexao.inicio >= date('now', 'start of month') AND conexao.inicio <= date('now', 'start of month', '+1 month', '-1 day') ORDER BY conexao.inicio;
//
         public function get_user_conect_mes(){
            $this->db->select('usuario.ativo, pessoa.nome, conexao.inicio');
            $this->db->from('conexao');
            $this->db->join('usuario', 'conexao.usuario_id = usuario.id');
            $this->db->join('pessoa', 'usuario.id = pessoa.id');
            $where = "conexao.inicio >= date('now', 'start of month') AND conexao.inicio <= date('now', 'start of month', '+1 month', '-1 day')";
            $this->db->where($where);
            $this->db->order_by('conexao.inicio', 'desc');
            return $this->db->get()->result();
    }
//
//quantidade de conexoes  por usuarios no mes de abril
//SELECT  count(conexao.usuario_id) as Qtd FROM conexao WHERE strftime("%m",inicio) = strftime("%m","now"); conexao.inicio >= date('now', 'start of month') AND conexao.inicio <= date('now', 'start of month', '+1 month', '-1 day');
         public function get_qtd_user_conect_mes(){
            $this->db->select('count(inicio) as Qtd');
            $this->db->from('conexao');
            $where = 'strftime("%m",inicio) = strftime("%m","now")';
            $this->db->where($where);
            $query = $this->db->get();
            return $query->row()->Qtd;
    }
//
//SELECT  strftime("%m","now","-1 month") as Mes, count(conexao.usuario_id) as Qtd  FROM conexao WHERE strftime("%m",inicio) = strftime("%m","now","-1 month");
//SELECT  strftime("%m","now","-1 month") as Mes, count(conexao.usuario_id) as Qtd  FROM conexao WHERE strftime("%m",inicio) = strftime("%m","now","-2 month");
//SELECT  strftime("%m","now","-1 month") as Mes, count(conexao.usuario_id) as Qtd  FROM conexao WHERE strftime("%m",inicio) = strftime("%m","now","-3 month");
//SELECT  strftime("%m","now","-1 month") as Mes, count(conexao.usuario_id) as Qtd  FROM conexao WHERE strftime("%m",inicio) = strftime("%m","now","-4 month");
//SELECT  strftime("%m","now","-1 month") as Mes, count(conexao.usuario_id) as Qtd  FROM conexao WHERE strftime("%m",inicio) = strftime("%m","now","-5 month");
//SELECT  strftime("%m","now","-1 month") as Mes, count(conexao.usuario_id) as Qtd  FROM conexao WHERE strftime("%m",inicio) = strftime("%m","now","-6 month");
//
//quantidade de usuarios conectados
//SELECT pessoa.nome, COUNT(*) FROM conexao JOIN usuario ON conexao.usuario_id = usuario.id JOIN pessoa ON usuario.pessoa_id = pessoa.id WHERE fim = '' GROUP BY conexao.usuario_id;
//
         public function get_qtd_user_conect(){
            $this->db->select('count(id) as Qtd');
            $this->db->from('conexao');
            $this->db->where('fim ', null);
            $query = $this->db->get();
            return $query->row()->Qtd;
    }
//




//Duraçao dos Acessos no Mês
//SELECT conexao.usuario_id, SUM(strftime('%s', conexao.fim) - strftime('%s', conexao.inicio)) AS Duracao, pessoa.nome FROM conexao JOIN usuario ON conexao.usuario_id = usuario.id JOIN pessoa ON usuario.pessoa_id = pessoa.id WHERE  inicio >= date('now', 'start of month') AND inicio <= date('now', 'start of month', '+1 month', '-1 day')AND fim >= date('now', 'start of month') AND fim <= date('now', 'start of month', '+1 month', '-1 day') GROUP BY  conexao.usuario_id;
         public function get_duracao_acesso_mes(){
             $this->db->select('conexao.usuario_id, pessoa.nome');
             $this->db->select_sum( 'strftime("%s", conexao.fim) - strftime("%s", conexao.inicio)', 'Duracao' );
             $this->db->from('conexao');
             $this->db->join('usuario', 'conexao.usuario_id = usuario.id');
             $this->db->join('pessoa', 'usuario.pessoa_id = pessoa.id');
             $where = "inicio >= date('now', 'start of month') AND inicio <= date('now', 'start of month', '+1 month', '-1 day') AND fim >= date('now', 'start of month') AND fim <= date('now', 'start of month', '+1 month', '-1 day')";
             $this->db->where($where);
             $this->db->group_by('conexao.usuario_id');
             return $this->db->get()->result();
            //return $this->db->query('SELECT conexao.usuario_id, SUM(strftime("%s", conexao.fim) - strftime("%s", conexao.inicio)) AS Duracao, pessoa.nome FROM conexao JOIN usuario ON conexao.usuario_id = usuario.id JOIN pessoa ON usuario.pessoa_id = pessoa.id WHERE  inicio >= date("now", "start of month") AND inicio <= date("now", "start of month", "+1 month", "-1 day") AND fim >= date("now", "start of month") AND fim <= date("now", "start of month", "+1 month", "-1 day") GROUP BY  conexao.usuario_id')->result();
    }
//Duraçao dos acessos geral
//SELECT conexao.usuario_id, SUM(strftime('%s', conexao.fim) - strftime('%s', conexao.inicio)) AS Duracao, pessoa.nome FROM conexao JOIN usuario ON conexao.usuario_id = usuario.id JOIN pessoa ON usuario.pessoa_id = pessoa.id  GROUP BY  conexao.usuario_id;
         public function get_duracao_acesso_geral(){
            $this->db->select('conexao.usuario_id, pessoa.nome');
            $this->db->select_sum('strftime("%s", conexao.fim) - strftime("%s", conexao.inicio)', 'Duracao');
            $this->db->from('conexao');
            $this->db->join('usuario', 'conexao.usuario_id = usuario.id');
            $this->db->join('pessoa', 'usuario.pessoa_id = pessoa.id');
            $this->db->group_by('conexao.usuario_id');
            return $this->db->get()->result();
    }

    /*
     * SQL1: select count(id) as qtd from conexao where strftime('%Y-%m', inicio) = strftime('%Y-%m','now');
     * SQL2: select count(id) as qtd from conexao where strftime('%Y-%m', inicio) = strftime('%Y-%m','now', '-1 month');
     */
    public function total_acessos_mes($back) {
   		$this->db->select('count(id) as "qtd"');
   		$this->db->from('conexao');

    	if (empty($back))
    		$where = 'strftime("%Y-%m", inicio) = strftime("%Y-%m","now","localtime")';
    	else
    		$where = 'strftime("%Y-%m", inicio) = strftime("%Y-%m","now","-'. $back .' month","localtime")';

   		$this->db->where($where);

   		$query = $this->db->get();

   		if ($query->row()->qtd == NULL)
   			return 0;
   		else
   			return $query->row()->qtd;
    }

    /*
     * $dia: Espera o formato Y-m-d (ex: 2016-05-01)
     * $hora: Espera o formato H (ex: 2016-05-01 08)
     */
    public function total_acessos_dia($dia, $hora = NULL) {
    	$this->db->select('count(id) as "qtd"');
    	$this->db->from('conexao');

		if ($hora) {
			$where = 'strftime("%Y-%m-%d", inicio) = "'. $dia .'"';
			$this->db->where('strftime("%H", inicio) = "'. $hora .'"');
		} else
    		$where = 'strftime("%Y-%m-%d", inicio) = "'. $dia .'"';


		$this->db->where($where);

		$query = $this->db->get();

		if ($query->row()->qtd == NULL)
			return 0;
		else
			return $query->row()->qtd;
    }

	public function total_ares_mes($back) {
        $this->db->select('count(id) as "qtd"');
        $this->db->from('usuario');

        if (empty($back))
            $where = 'strftime("%Y-%m", expedida) = strftime("%Y-%m","now","localtime")';
        else
            $where = 'strftime("%Y-%m", expedida) = strftime("%Y-%m","now","-'. $back .' month","localtime")';

        $this->db->where($where);

        $query = $this->db->get();

        if ($query->row()->qtd == NULL)
            return 0;
        else
            return $query->row()->qtd;
    }

    /*
     * $dia: Espera o formato Y-m-d (ex: 2016-05-01)
     * $hora: Espera o formato H (ex: 2016-05-01 08)
     */
    public function total_ares_dia($dia, $hora = NULL) {
        $this->db->select('count(id) as "qtd"');
        $this->db->from('usuario');

        if ($hora) {
            $where = 'strftime("%Y-%m-%d", expedida) = "'. $dia .'"';
            $this->db->where('strftime("%H", expedida) = "'. $hora .'"');
        } else
            $where = 'strftime("%Y-%m-%d", expedida) = "'. $dia .'"';


        $this->db->where($where);

        $query = $this->db->get();

        if ($query->row()->qtd == NULL)
            return 0;
        else
            return $query->row()->qtd;
    }
    
    public function total_bytes_mes() {
    	$this->db->select_sum('bytes_in', 'bytes_in');
    	$this->db->select_sum('bytes_out', 'bytes_out');
    	$this->db->from('conexao');
    	$this->db->where('strftime("%Y-%m", fim) = strftime("%Y-%m","now","localtime")');
    	
    	$query = $this->db->get();
    	return $query->row_array();
    }

}
