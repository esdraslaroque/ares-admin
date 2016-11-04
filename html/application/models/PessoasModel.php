<?php
class PessoasModel extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	public function getPessoa($login) {
		$this->db->select('*');
		$this->db->where('login', $login);
		$query = $this->db->get('pessoa');
		
		if ($query)
			return $query->row_array();

		return false;
	}
	
	public function addPessoa($login, $nome, $email) {
		$data = array(
				'login' => $login,
				'nome' => urldecode($nome),
				'email' => urldecode($email)
		);
		
		$this->db->insert('pessoa', $data);
	}
	
	public function editPessoa($pessoa_id, $nome, $email) {
		$data = array();
		
		if ($nome)
			$data['nome'] = urldecode($nome);
		
		if ($email)
			$data['email'] = urldecode($email);
		
		$this->db->where('id', $pessoa_id);
		$this->db->update('pessoa', $data);
		
		if ($this->db->affected_rows() == 1)
			return true;
		else
			return false;
	}
}