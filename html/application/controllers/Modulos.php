<?php
class Modulos extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->helper('url_helper');
		$this->load->library('session');

		if (! $this->session->admin_username)
			redirect(base_url('/app/admin'));
		
	}

	public function view ($page = 'usuarios') {
		$data ['modulo'] = 'Gerenciador';
		$data ['versao'] = '0.1';

		if ( !$this->session->admin_username ) {
			/*--------------------------
			 * Carregar página de autenticação
			 *--------------------------*/
			$data ['js'] = 'admin_login';
			$data ['css'] = 'admin_login';
				
			$this->load->view ('templates/header1', $data);
			$this->load->view ('pages/admin_login');
			$this->load->view ('templates/footer1', $data);
				
			return;
		} else {
			$data ['admin_username'] = $this->session->admin_username;
		}
		
		
		if (! file_exists ( APPPATH . 'views/pages/mod_' . $page . '.php' )) {
			$data['modulo'] = $page;
			$this->load->view ('pages/mod_emconstrucao', $data);
			return;
		}
		
		$this->load->view ('pages/mod_' . $page, $data);
	}
}