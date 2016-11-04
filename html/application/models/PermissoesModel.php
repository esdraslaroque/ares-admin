<?php
class PermissoesModel extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	public function getPermissoes($usuario_id = FALSE) {
		$this->db->select('*');
		
		if ($usuario_id)
			$this->db->where('usuario_id', $usuario_id);
		
		$query = $this->db->get('permissao');
		
// 		if ($usuario_id) {
// 			$this->db->select('*');
// 			$this->db->where('usuario_id', $usuario_id);
			
// 			if ($this->db->count_all_results('permissao') > 1)
// 				return $query->result_array();
// 			else
// 				return $query->row_array();
// 		}
		
		return $query->result_array();
	}
	
	public function addPerm($usuario_id, $regra_id, $admin_id) {
		$data = array(
				'admin_id' => $admin_id,
				'usuario_id' => $usuario_id,
				'regra_id' => $regra_id
		);
		
		$this->db->insert('permissao', $data);
	}
	
	public function delPerm($permissao_id) {
		$this->db->delete('permissao', array('id' => $permissao_id));
	}
}