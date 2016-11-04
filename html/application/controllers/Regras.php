<?php
class Regras extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('RegrasModel');
		$this->load->helper('indent_json');
		// 		$this->load->library('session');
	}

	public function regras() {
		$regras = $this->RegrasModel->getRegras();
	
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($regras)) );
	}
	
	public function grupo_regras() {
		$grupos = $this->RegrasModel->getGrupos();
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($grupos)) );
	}

	public function regra_acao($regra_id, $acao) {
		$this->RegrasModel->setAcao($regra_id, $acao);
	}

	public function remove_regra($regra_id) {
		$this->RegrasModel->removeRegra($regra_id);
	}
	
	public function add_regra($admin_id, $grupo_regra_id = 1) {
		$regra = (array) json_decode($this->input->post('object'));
		
		$this->RegrasModel->addRegra($regra['destino'], $regra['proto'], $regra['servico'], $regra['descricao'], $admin_id, $grupo_regra_id);
		
		$added = $this->RegrasModel->getRegraAdd($admin_id);
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($added)) );
	}
	
	public function add_grupo($descricao = null) {
		if (empty($descricao))
			show_error('Parameter Needed');
		
		$this->RegrasModel->addGrupo($descricao);
	}

	public function remove_grupo($grupo_id) {
		$this->RegrasModel->removeGrupo($grupo_id);
	}

	public function edita_regra($admin_id, $grupo_regra_id = 1) {
		$regra = (array) json_decode($this->input->post('object'));
		
		$this->RegrasModel->editRegra($regra['id'], $regra['descricao'], $regra['destino'], $regra['proto'], $regra['servico'], $admin_id, $grupo_regra_id);
		
		$data = array(
				'regra_id' => $regra['id'],
				'descricao' => urldecode($regra['descricao']),
				'destino' => $regra['destino'],
				'proto' => $regra['proto'],
				'servico' => $regra['servico'],
				'admin_id' => $admin_id,
				'grupo_regra_id' => $grupo_regra_id
		);
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($data)) );
	}
}