<?php
class ConfigsModel extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	public function getAll() {
		$this->db->select('chave, valor');
		
		$query = $this->db->get('config');
		
		return $query->result_array();
	}
	
	public function getConfig($chave) {
		$this->db->select('valor');
		$this->db->where('chave', $chave);
		
		$query = $this->db->get('config');
		
		return $query->row()->valor;
	}
	
}