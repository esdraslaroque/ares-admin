<?php
class AdminsModel extends CI_Model {
	public function __construct() {
		$this->load->database();
	}

	public function getAdmins($login = NULL) {
		$this->db->select('admin.*, pessoa.login, pessoa.nome');
		$this->db->select('(select count(id) from usuario where id = admin.id) as "isuser"');
		$this->db->from('admin');
		$this->db->join('pessoa','admin.id = pessoa.id');
		$this->db->where_not_in('admin.id', array(1)); // Escapando usuário admin interno
		
		if ($login)
			$this->db->where('pessoa.login', $login);
		
		$query = $this->db->get();
		
		if ($login)
			return $query->row_array();
		
		return $query->result_array();
	}
	
	public function isAdmin($login) {
		$this->db->select('admin.id, pessoa.nome, pessoa.email, admin.perfil, (select 0) as "cod"');
		$this->db->from('pessoa');
		$this->db->join('admin','admin.id = pessoa.id');
		$this->db->where('pessoa.login', $login);
		$this->db->where('admin.ativo', 1);
		
		$query = $this->db->get();
		
		return $query->row_array();
	}

	public function setStatus($admin_id, $status) {
		$data = array(
				'ativo' => $status
		);
	
		$this->db->where('id', $admin_id);
		$this->db->update('admin', $data);
	}

	public function removeAdmin($admin_id) {
		$this->db->delete('admin', array('id' => $admin_id));
	}

	public function editAdmin($admin_id, $perfil) {
		$data = array('perfil' => $perfil);

		$this->db->where('id', $admin_id);
		$this->db->update('admin', $data);
		
		if ($this->db->affected_rows() == 1)
			return true;
		else
			return false;
	}
	
	public function addAdmin($pessoa_id, $perfil) {
		$data = array(
				'id' => $pessoa_id,
				'perfil' => $perfil,
				'ativo' => 0
		);
		
		$this->db->insert('admin', $data);

		if ($this->db->affected_rows() == 1)
			return true;
		else
			return false;
	}
}