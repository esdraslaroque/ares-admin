<?php
class Permissoes extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('PermissoesModel');
		$this->load->helper('indent_json');
		$this->load->helper('url_helper');
		$this->load->library('session');
		
		if (! $this->session->admin_username)
			redirect(base_url('/app/admin'));
	}

	public function permissoes($usuario_id = NULL) {
		if ($usuario_id)
			$perms = $this->PermissoesModel->getPermissoes($usuario_id);
		else
			$perms = $this->PermissoesModel->getPermissoes();
	
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($perms)) );
	}
	
	public function add($usuario_id, $admin_id) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$permissoes = (array) json_decode($this->input->post('object'));
		
		foreach ($permissoes as $perm) {
			$this->PermissoesModel->addPerm($usuario_id, $perm, $admin_id);
		}
	}

	public function edit($usuario_id, $admin_id) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$permissoes = (array) json_decode($this->input->post('object'));
		$perms = $this->PermissoesModel->getPermissoes($usuario_id);
		$exist_perms = array();
		
		foreach ($perms as $exist)
			array_push($exist_perms, $exist['regra_id']);
		
		foreach ($permissoes as $perm)
			if (! in_array($perm, $exist_perms))
				$this->PermissoesModel->addPerm($usuario_id, $perm, $admin_id);
		
		foreach ($perms as $perm)
			if (! in_array($perm['regra_id'], $permissoes))
				$this->PermissoesModel->delPerm($perm['id']);
	}
	
}