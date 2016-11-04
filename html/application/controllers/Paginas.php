<?php
class Paginas extends CI_Controller {

	public function __construct() {
		parent::__construct ();
		$this->load->library('session');
		$this->load->library('user_agent');
	}
	
	public function view ($page = 'login') {
		if (! file_exists ( APPPATH . 'views/pages/' . $page . '.php' )) {
			show_404 ();
		}
		
		$modulo = 'Autenticador';
		$versao = '2.1';
		$css	= FALSE;
		
		if ( $page == 'admin') {
			$modulo = 'Gerenciador';
			$versao = '0.8';
		}

		$data ['modulo'] = $modulo;
		$data ['versao'] = $versao;
		$data ['js'] = $page;
		$data ['css'] = $page;
		
		if ( $page == 'admin' && !$this->session->admin_username ) {
			/*--------------------------
			 * Carregar página de autenticação
			 *--------------------------*/
			$data ['js'] = 'admin_login';
			$data ['css'] = 'admin_login';
			
			$this->load->view ('templates/header1', $data);

			/* Verificando o suporte ao browser */
			if ($this->agent->is_mobile()) {
				
				$data ['msg_err'] = 'Este sistema não está acessível a partir de dispositivos móveis';
				$this->load->view ('templates/unsupported', $data);
				
			} else if ($this->agent->browser() != 'Chrome' && $this->agent->browser() != 'Firefox') {
				
				$data ['msg_err'] = 'Seu navegador <b>'. $this->agent->browser() .'</b>
									 não é suportado por este sistema, atualmente. Tente usar outro navegador.';
				$this->load->view ('templates/unsupported', $data);
				
			} else if ($this->agent->browser() == 'Chrome' && intval(substr($this->agent->version(), 0, 2)) < 50 ) {
				
				$data ['msg_err'] = 'A versão deste navegador <b>'. $this->agent->browser() .'</b>
									 não é suportada por este sistema. Tente usar outro navegador ou atualizar este para uma versão recente.';
				$this->load->view ('templates/unsupported', $data);
				
			} else 			
				$this->load->view ('pages/admin_login');
			
			$this->load->view ('templates/footer1', $data);
			
			return;
		} else {
			$data ['admin_username'] = $this->session->admin_username;
		}
		
// 		if ($this->agent->browser() == 'Internet Explorer' && $page == 'login')
// 			$data ['js'] = 'ielogin';
		
		$this->load->view ('templates/header1', $data);
		
// 		if ($this->agent->browser() == 'Internet Explorer' && $page != 'testelogin') {// && intval(substr($this->agent->version(), 0, 2)) < 9 )
// 			$data ['msg_err'] = 'A versão do seu navegador, <b>'. $this->agent->browser() .'</b>,
// 								 já não é mais suportado por este sistema. <br><br> Você deve atualizá-lo para versão 9 ou mais recente.<br><br>
// 								 Você também pode utilizar outro navegador para autenticar, por exemplo, Mozilla Firefox ou Google Chrome
// 								 acessando o endereço http://ares.sefa.pa.gov.br/';
// 			$this->load->view ('templates/unsupported', $data);
// 			$this->load->view ('pages/ielogin');
// 		} else
			$this->load->view ('pages/' . $page);
		$this->load->view ('templates/footer1', $data);
	}
}