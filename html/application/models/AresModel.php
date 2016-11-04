<?php
class AresModel extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	public function getPessoa($login = FALSE) {
		if ($login === FALSE) {
			$query = $this->db->get('pessoa');
			return $query->result_array();
		}
		
		$query = $this->db->get_where('pessoa', array('login' => $login));
		return $query->row_array();
	}
	
	public function getPidByIP($remote_addr = NULL) {
		if (empty($remote_addr))
			show_error('Undefined REMOTE_ADDR');
		
		$this->db->select('pid');
		$this->db->from('conexao');
		$this->db->where('ip_cliente', $remote_addr);
		$this->db->where('fim', NULL);
		$this->db->order_by('inicio','DESC');
		$this->db->limit(1);
		
		$row = $this->db->get()->row();
		
		if (isset($row))
			return $row->pid;
		else
			return NULL;
		
	}
	
	public function getRulesByLogin($login) {
		$this->db->select('permissao.id, regra.descricao');
		$this->db->from('permissao');
		$this->db->join('regra','regra.id = permissao.regra_id');
		$this->db->join('pessoa','permissao.usuario_id = pessoa.id');
		$this->db->where('pessoa.login', $login);
		
		$query = $this->db->get();
		
		return $query->result_array();
	}
}