<?php
class Autenticador extends CI_Controller {
	
	public function __construct() {
		parent::__construct ();
		$this->load->library('session');
		$this->load->helper('url_helper');
	}
	
	public function randomImage() {

		$alphanum = "abcdefghijklmnopqrstuvxyz0123456789";
		$rand = substr(str_shuffle($alphanum), 0, 5);
		$bgNum = rand(1,7);
		$image = imagecreatefromjpeg(base_url('/images/background'.$bgNum.'.jpg'));
		$textColor = imagecolorallocate($image, 0, 0, 0);

		$this->session->hashcode = md5($rand);
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-type: image/jpeg');

		imagestring($image, 5, 5, 8, $rand, $textColor);
		imagejpeg($image);
		imagedestroy($image);
		
	}
	
	public function codeValidate($code) {
		$this->load->model('AresModel');
		
		sleep(1);
		
		if ( md5($code) == $this->session->hashcode )
			echo 0;
		
		else {
			echo 1;
					
			/*
			 * Logando falha no Sistema Operacional via SYSLOG
			 */
			$pid = $this->AresModel->getPidByIP($_SERVER['REMOTE_ADDR']);
			
			if (!empty($pid)) {
				openlog('ares['.$pid.']', LOG_ODELAY, LOG_DAEMON);
				syslog(LOG_ERR, 'ERROR - Captcha incorreto na verificação de chave');
				closelog();
			}
		}
	}
	
// 	public function uploadKeyBkp() {
// 		$uploaddir = '/tmp/';
// 		$uploadfile = $uploaddir . basename($_FILES['cChave']['name']);
		
// 		if (move_uploaded_file($_FILES['cChave']['tmp_name'], $uploadfile))
// 			echo basename($_FILES['cChave']['name']);
// 		else
// 			echo 1;
// 	}
	
	public function uploadKey() {
		if ($_FILES['cChave']['size'] > 1030) {
			echo 2;
			return;
		}
		
		$k = str_replace(' ', '-', basename($_FILES['cChave']['name']));
		$k = preg_replace('/[^A-Za-z0-9\-\.]/', '', $k);
		$uploadfile = '/tmp/' . $k;

		if (move_uploaded_file($_FILES['cChave']['tmp_name'], $uploadfile))
			echo basename($_FILES['cChave']['name']);
		else
			echo 1;
	}
	
// 	public function showRulesBk($login = NULL) {
// 		if (empty($login))
// 			show_error('Parameter Needed');
		
// 		$this->load->model('AresModel');

// 		$rules = $this->AresModel->getRulesByLogin($login);
		
// 		$this->output->set_content_type('application/json')->set_output('{"data": '. json_encode($rules) .'}');
// 	}
	
	public function showRules($login = NULL) {
		if (empty($login))
			show_error('Parameter Needed');
	
		$this->load->model('AresModel');

		$rules = $this->AresModel->getRulesByLogin($login);

		$this->output->set_content_type('application/json')->set_output(json_encode($rules) );
	}
	
	public function admin($logout = FALSE) {
		if ($logout == 'logoff') {
			$this->session->sess_destroy();
			redirect(base_url('/app/admin'));
		}
		
		if ( ! $this->input->server('REMOTE_USER') && ! $this->input->server('EXTERNAL_AUTH_ERROR') )
			redirect(base_url('/app/admin'));
		
		$resposta = array();
		$login = explode('@', $this->input->server('REMOTE_USER'));
		
		if ($this->input->server('EXTERNAL_AUTH_ERROR')) {
			$resposta['cod'] = 1;
			$resposta['msg'] = $this->input->server('EXTERNAL_AUTH_ERROR');
			
			$this->output->set_content_type('application/json')->set_output( json_encode($resposta) );
			return;
		}
		
		$this->load->model('AdminsModel');
		
		$dados = $this->AdminsModel->isAdmin($login[0]);
		
		if ( !empty($dados) ) {

			$this->session->admin_id = $dados['id'];
 			$this->session->admin_nome = $dados['nome'];
 			$this->session->admin_email = $dados['email'];
 			$this->session->admin_perfil = $dados['perfil'];
 			$this->session->admin_username = $login[0];
 			redirect(base_url('/app/admin'));

		} else {
			$resposta['cod'] = 1;
			$resposta['msg'] = 'Usuário inválido';
			$this->output->set_content_type('application/json')->set_output( json_encode($resposta) );
		}
			
	}
	
}