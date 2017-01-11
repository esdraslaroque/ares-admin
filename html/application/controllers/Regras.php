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
		
		if (!$this->ipCheck($regra['destino']) || !$this->portCheck($regra['servico']))
			show_error('Fields with miss formatted');
		
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
		
		$data = array();
		
		if (!$this->ipCheck($regra['destino']))
			$data = array('cod' => 1, 'msg' => 'Falha de edição. Destino inválido');
		
		else if (!$this->portCheck($regra['servico']))
			$data = array('cod' => 1, 'msg' => 'Falha de edição. Serviço inválido');
		
		else if ($this->RegrasModel->editRegra($regra['id'], $regra['descricao'], $regra['destino'], $regra['proto'], $regra['servico'], $admin_id, $grupo_regra_id)) {
			$data = array(
					'cod' => 0,
					'regra_id' => $regra['id'],
					'descricao' => urldecode($regra['descricao']),
					'destino' => $regra['destino'],
					'proto' => $regra['proto'],
					'servico' => $regra['servico'],
					'admin_id' => $admin_id,
					'grupo_regra_id' => $grupo_regra_id
			);
		} else
			$data = array('cod' => 1, 'msg' => 'Falha de edição. Verifique duplicidade');
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($data)) );
	}
	
	private function portCheck($port) {
		if (!preg_match('/^[0-9]{0,5}[\:]{0,1}[0-9]{1,5}$/', $port))
			return FALSE;
		
		if (strpos($port, ':')) {
			$range = explode(':', $port);
			
			if ($range[0] >= $range[1] || $range[0] > 65535 || $range[1] > 65535)
				return FALSE;
		}
		
		if ($port > 65535)
			return FALSE;
		
		return TRUE;
	}
	
	private function ipCheck($ip) {
		if (!preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/{0,1}([8]{1}|[123]{1}[0-9]{1}){0,1}$/', $ip))
			return FALSE;
		
		if (strpos($ip, '/')) {
			$oct = explode('/', $ip);
				
			if ($oct[1] > 30)
				return FALSE;
		}
	
		return TRUE;
	}
	
}