<?php
class Registros extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		// 		$this->load->library('session');
	}
	
	public function log() {
		$output = file_get_contents('http://127.0.0.1/log');
		$this->output->set_content_type('application/json')->set_output( $output );
	}

	public function acessos($de, $ate, $login = NULL) {
		$this->load->model('ConexoesModel');
		$this->load->helper('indent_json');
		
		if (empty($login))
			$this->output->set_content_type('application/json')
			 	 ->set_output( indent_json(json_encode( $this->ConexoesModel->getAcessos($de, $ate) )) );
		else
			$this->output->set_content_type('application/json')
				 ->set_output( indent_json(json_encode( $this->ConexoesModel->getAcessosByLogin($de, $ate, $login) )) );
	}
}