<?php
class ConexoesModel extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	public function getAtivas() {
		$this->db->select('*');
		$this->db->where('fim', NULL);
		
		$query = $this->db->get('conexao');
		
		return $query->result_array();
	}

	public function getAcessos($de, $ate) {
		$this->db->select('pessoa.login, strftime("%d/%m/%Y %H:%M",conexao.inicio) as "ult_acesso",
						   (select count(id) from conexao where usuario_id = pessoa.id and conexao.inicio between datetime("'. $de .'") and datetime("'. $ate .'","+1 day")) as "qtd_acessos",
						   (select SUM(bytes_in + bytes_out) from conexao where usuario_id = pessoa.id and conexao.inicio between datetime("'. $de .'") and datetime("'. $ate .'","+1 day")) as "trafego",
						   (select SUM(cast((julianday(fim) - julianday(inicio)) * 24 * 60 * 60 as Integer)) from conexao where usuario_id = pessoa.id and conexao.inicio between datetime("'. $de .'") and datetime("'. $ate .'","+1 day")) as "duracao"');
		$this->db->from('pessoa');
		$this->db->join('conexao','pessoa.id = conexao.usuario_id');
		$this->db->where('conexao.inicio between datetime("'. $de .'") and datetime("'. $ate .'", "+1 day")');
		$this->db->where('conexao.fim IS NOT', NULL);
		$this->db->group_by('pessoa.login');
		
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function getAcessosByLogin($de, $ate, $login) {
		$this->db->select('pid, interface, ip_cliente, strftime("%d/%m/%Y %H:%M",conexao.inicio) as "inicio", strftime("%d/%m/%Y %H:%M",conexao.fim) as "fim",
						   cast((julianday(fim) - julianday(inicio)) * 24 * 60 * 60 as Integer) as "duracao", (conexao.bytes_in + conexao.bytes_out) as "trafego", autenticado');
		$this->db->from('pessoa');
		$this->db->join('conexao','pessoa.id = conexao.usuario_id');
		$this->db->where('pessoa.login', $login);
		$this->db->where('conexao.inicio between datetime("'. $de .'") and datetime("'. $ate .'","+1 day")');
		$this->db->where('conexao.fim IS NOT', NULL);
		$this->db->order_by('conexao.inicio');
		
		$query = $this->db->get();
		return $query->result_array();
	}
}