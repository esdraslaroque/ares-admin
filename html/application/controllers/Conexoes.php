<?php
class Conexoes extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('ConexoesModel');
		$this->load->helper('indent_json');
		// 		$this->load->library('session');
	}

	public function ativas() {
		$this->output->set_content_type('application/json')->
			   set_output( indent_json(json_encode( $this->ConexoesModel->getAtivas() )));
	}
	
}