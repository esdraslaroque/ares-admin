<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configs extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('ConfigsModel');
		$this->load->helper('indent_json');
		$this->load->helper('url_helper');
		$this->load->library('session');
	}

	public function all() {
		if (! $this->session->admin_username)
			redirect(base_url('/app/admin'));

		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');

		$confs = $this->ConfigsModel->getAll();
		
		$configs = array();
		
		foreach ($confs as $conf)
			$configs[$conf['chave']] = $conf['valor'];
	
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($configs)) );
	}
	
	public function sendmail_alert($tipo = NULL) {
		if (! is_numeric($tipo))
			exit ('Numeric Parameter Needed');
		
		/*--------------------------------------------
		 * Limitando acesso somente para o host local
		 * Configuração deve ser chamada via cron
		 *-------------------------------------------*/
		if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1')
			exit ('Access Denied');
		
		if (! $tipo)
			exit ('Parameter Needed');
		
		$this->load->model('UsuariosModel');
		
		$s = NULL;
		
		if ($tipo == 15)
			$s = 0;
		if ($tipo == 7)
			$s = 1;
		
		$lista = $this->UsuariosModel->getUsersExpireIn($tipo, $s);
		
		if (empty($lista))
			exit ('Sem contas a vencer ..');
		
		$this->load->library('email');
		
		$this->email->initialize(array(
				'protocol' 	=> 'smtp',
				'smtp_host' => $this->ConfigsModel->getConfig('email_smtp'),
				'smtp_port' => 25,
				'mailtype'  => 'html'
		));

		$img = '/var/www/html/images/icone_email.png';
		$this->email->attach($img);
		$cid = $this->email->attachment_cid($img);
		
		if ($tipo == 15 || $tipo == 7) {
			
			if ($tipo == 7) {
				$textorede = 'Chaves ARES que vencerão em 7 dias:<br>';
				$textorede .= '<table style="color: #565656; font-family: Georgia, serif; font-size: 16px;" cellspacing="4px">
								<thead>
									<tr>
										<td><b>Nome</b></td>
										<td><b>Login</b></td>
										<td><b>Email</b></td>
										<td><b>Validade<b></td>
									</tr>
								</thead>';
			}
			
			/*
			 * Alerta para cada usuário listado
			 */
			foreach ($lista as $user) {
				$nome = explode(' ', $user['nome']);
				$expiracao = explode('-', $user['validade']);
				$validade = $expiracao[2] .'/'. $expiracao[1] .'/'. $expiracao[0];
				
				$texto = 'Seu tempo de concessão para o acesso residencial (ARES) com o usuário 
						 <b>'. $user['login'] .'</b>, está acabando. A validade de sua chave vai até dia '. $validade .'.<br><br>
						 Caso necessite de mais tempo, será necessário a renovação desta chave, implicando em 
						 um novo processo no SIAT, tramitado para a DTI desta secretaria.<br><br>';
			
				$data = array(
						'nome' => $nome[0],
						'sobrenome' => end($nome),
						'cid' => $cid,
						'texto' => $texto
				);
				
				$msg = $this->load->view('templates/email.php', $data, TRUE);
				
				$this->email->from($this->ConfigsModel->getConfig('email_remetente'), 'Sistema ARES');
				$this->email->to( $user['email'] );
				$this->email->reply_to('noreply@sefa.pa.gov.br');
				$this->email->subject('Sua chave está expirando');
				$this->email->message($msg);
				
				$this->email->send();
				
				if ($tipo == 15)
					$this->UsuariosModel->setAviso($user['id'], 1);
				
				if ($tipo == 7)
					$this->UsuariosModel->setAviso($user['id'], 2);
				
				if ($tipo == 7) {
					$textorede .= '<tr><td>'. $nome[0] .' '. end($nome) .'</td>';
					$textorede .= '<td>'. $user['login'] .'</td>';
					$textorede .= '<td>'. $user['email'] .'</td>';
					$textorede .= '<td>'. $validade .'</td></tr>';
				}
			}
			
			if ($tipo == 7) {
				$textorede .= '</table><br>';
				
				$data = array(
						'cid' => $cid,
						'texto' => $textorede
				);
				
				$msg = $this->load->view('templates/email_rede.php', $data, TRUE);
				
				$this->email->from($this->ConfigsModel->getConfig('email_remetente'), 'Sistema ARES');
				$this->email->to('rede@sefa.pa.gov.br');
				$this->email->reply_to('noreply@sefa.pa.gov.br');
				$this->email->subject('Acessos Expirando em 7 dias');
				$this->email->message($msg);
				
				$this->email->send();
			}
		} else
			exit ('Periodo nao previsto');
		
	}

	public function set($conf) {
		if (! $this->session->admin_username)
			redirect(base_url('/app/admin'));
		
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$config = (array) json_decode($this->input->post('object'));
		
		$ret = FALSE;
		$data = array('cod' => 0, 'msg' => 'Configurações de '.strtoupper($conf).' gravadas');
		
		if ($conf == 'ad') {
			$ret = $this->ConfigsModel->setConfig('ad_user', $config['ad_user']);
			$ret = $this->ConfigsModel->setConfig('ad_pass', $config['ad_pass']);
			$ret = $this->ConfigsModel->setConfig('ad_server', $config['ad_server']);
			$ret = $this->ConfigsModel->setConfig('ad_port', $config['ad_port']);
		}
		
		if ($conf == 'email') {
			$ret = $this->ConfigsModel->setConfig('email_smtp', $config['email_smtp']);
			$ret = $this->ConfigsModel->setConfig('email_remetente', $config['email_remetente']);
			
			switch ($config['tipo']) {
				case 1:
					$ret = $this->ConfigsModel->setConfig('email_mensagem_novo', $config['msg']);
					break;
				
				case 2:
					$ret = $this->ConfigsModel->setConfig('email_mensagem_renovacao', $config['msg']);
					break;
				
				case 3:
					$ret = $this->ConfigsModel->setConfig('email_mensagem_kit', $config['msg']);
					break;
			}
		}
		
		if (! $ret)
			$data = array('cod' => 1, 'msg' => 'Uma ou mais campos falharam');
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($data)) );
	}
	
}