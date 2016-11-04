<?php
class Admins extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('AdminsModel');
		$this->load->helper('indent_json');
		$this->load->helper('url_helper');
		$this->load->library('session');
		
		if (! $this->session->admin_username)
			redirect(base_url('/app/admin'));
	}

	public function admins($login = NULL) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		if ($login)
			$admins = $this->AdminsModel->getAdmins($login);
		else
			$admins = $this->AdminsModel->getAdmins();
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($admins)) );
	}
	
	public function info(){
		$dados = array();
		
		if ($this->session->admin_username) {
			$dados['id'] = $this->session->admin_id;
			$dados['username'] = $this->session->admin_username;
			$dados['email'] = $this->session->admin_email;
			$dados['perfil'] = $this->session->admin_perfil;
			$dados['nome'] = $this->session->admin_nome;
		} else
			show_404();
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($dados)) );
	}

	public function ativa_admin($admin_id, $status) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$this->AdminsModel->setStatus($admin_id, $status);
	}
	
	public function remove_admin($admin_id) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$this->AdminsModel->removeAdmin($admin_id);
	}

	public function edita_admin($admin_id) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$admin = (array) json_decode($this->input->post('object'));
		
		$this->load->model('PessoasModel');
		
		if ( ! $this->PessoasModel->editPessoa($admin_id, $admin['nome'], NULL))
			show_error('Error while editing nome informations');
		
		if ( ! $this->AdminsModel->editAdmin($admin_id, $admin['perfil']))
			show_error('Error while editing perfil informations');
	}

	public function add_admin() {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$admin = (array) json_decode($this->input->post('object'));
	
		$this->load->model('PessoasModel');
		$pessoa = $this->PessoasModel->getPessoa($admin['login']);
	
		if (! $pessoa) {
			$this->PessoasModel->addPessoa($admin['login'], $admin['nome'], $admin['email']);
			$pessoa = $this->PessoasModel->getPessoa($admin['login']);
		}
	
		if ($pessoa) {
			if (! $this->AdminsModel->addAdmin($pessoa['id'], $admin['perfil']))
				show_error('Error while creating admin register in database');
			
			$output = $this->AdminsModel->getAdmins($admin['login']);
			$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($output)) );
		} else
			show_error('Error while retrieve admin info from pessoa in database');
	}
}