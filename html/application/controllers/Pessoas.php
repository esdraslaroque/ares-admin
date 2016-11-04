<?php
class Pessoas extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('PessoasModel');
		$this->load->helper('indent_json');
// 		$this->load->library('session');
	}
	
	public function pessoas($login) {
		$people = $this->PessoasModel->getPessoa($login);
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($people)) );
	}
}