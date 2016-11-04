<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('UsuariosModel');
		$this->load->helper('indent_json');
		$this->load->helper('url_helper');
		$this->load->library('session');

		if (! $this->session->admin_username)
			redirect(base_url('/app/admin'));
	}
	
	public function usuarios($login = NULL) {
		if ($login)
			$users = $this->UsuariosModel->getUsuarios($login);
		else {
			$datausers = $this->UsuariosModel->getUsuarios();
			$users = array();
			
			foreach ($datausers as $index) {
				$index['ad_status'] = $this->ad_status($index['login']);
				array_push($users, $index);
			}
		}
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($users)) );
	}
	
	public function ativa_usuario($usuario_id, $ativo) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');

		$this->UsuariosModel->setAtivo($usuario_id, $ativo);
	}
	
	public function renova_chave($login, $validade, $admin_id, $usuario_id, $processo = NULL) {
		if ($this->session->admin_perfil == 3)
			show_error('Access Denied');

		$output = NULL;
		$ok = TRUE;
			
		if ($processo != NULL) {
			if ($this->UsuariosModel->checkProcesso($processo)) {
				
				$output = '{"cod":1, "msg":"Número de processo SIAT duplicado"}';
				$ok = FALSE;
			
			}
		}

		if ($ok) {
			$output = file_get_contents('http://127.0.0.1/gpgManage?op=renew&username='. $login .'&valid='. $validade);
			$this->UsuariosModel->updateAdmin($admin_id, $usuario_id);
			$this->UsuariosModel->updateProcesso($processo, $usuario_id);
			$this->UsuariosModel->setAviso($usuario_id, 0);
			$this->sendmail($login, 2);

			/* Temporario Para periodo de migração do x-oc-ipsec */
			$this->gera_kit($login);
		} 

		$this->output->set_content_type('application/json')->set_output( $output );
	}

	public function consulta_chave($login) {
		$output = file_get_contents('http://127.0.0.1/gpgManage?op=query&username='. $login);
		$this->output->set_content_type('application/json')->set_output( $output );
	}
	
	public function remove_ares($usuario_id) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$this->load->helper('file');
		$dados = $this->UsuariosModel->getHome($usuario_id);

		delete_files($dados['home'] . '/.gnupg', TRUE);
		rmdir($dados['home'] . '/.gnupg');
		
		delete_files($dados['home'], TRUE);
		rmdir($dados['home']);
		
		$this->UsuariosModel->removeUsuario($usuario_id);
	}
	
	public function gera_kit($login = NULL, $mail = FALSE) {
		if ($this->session->admin_perfil == 3)
			show_error('Access Denied');
		
		if (empty($login))
			show_error('Parameters Needed');
		
		$dados = $this->UsuariosModel->getHome(FALSE, $login);

		$output = file_get_contents('http://127.0.0.1/kitAres?username='. $login .'&home='. $dados['home']);
		$this->output->set_content_type('application/json')->set_output( $output );
		
		if ($mail)
			$this->sendmail($login, 3);
	}

	public function conexao_info($usuario_id = NULL) {
		if (empty($usuario_id))
			show_error('Parameter Needed');
		
		$con_info = $this->UsuariosModel->getConInfo($usuario_id);
		
		$this->output->set_content_type('application/json')->set_output(json_encode($con_info));
	}

	public function add_usuario($validade, $admin_id) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$usuario = (array) json_decode($this->input->post('object'));
		
		$this->load->model('PessoasModel');
		$data = $this->PessoasModel->getPessoa($usuario['login']);
		
		if (! $data) {
			$this->PessoasModel->addPessoa($usuario['login'], $usuario['nome'], $usuario['email']);
			$data = $this->PessoasModel->getPessoa($usuario['login']);
		}
		
		if ($data) {
			$this->UsuariosModel->addUsuario($usuario['login'], 1, $data['id'], $usuario['processo'], $admin_id);
			$output = file_get_contents('http://127.0.0.1/gpgManage?op=new&username='. $usuario['login'] .'&valid='. $validade);
			$this->output->set_content_type('application/json')->set_output( $output );
			
			$this->sendmail($usuario['login'], 1);
		} else
			show_error('Error while creating user register in database');
	}
	
	public function edita_usuario($usuario_id) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$usuario = (array) json_decode($this->input->post('object'));

		$this->load->model('PessoasModel');
		if (! $this->PessoasModel->editPessoa($usuario_id, $usuario['nome'], $usuario['email']))
			show_error('Error while editing user informations');
	}
	
	private function ad_status ($login) {
		$this->load->model('LdapAdModel');
		$cod = 0;
		
		if (! $this->LdapAdModel->isValid($login) )
			$cod = 1;
		
		else if ( $this->LdapAdModel->isBlocked($login) )
			$cod = 2;
		
		else if (! $this->LdapAdModel->inAresGroup($login) )
			$cod = 3;
		
		return $cod;
	}

	public function ad_membros($ad_group) {
		if ($this->session->admin_perfil != 1)
			show_error('Access Denied');
		
		$this->load->model('LdapAdModel');
		$ad_users = array();
		
		foreach ($this->LdapAdModel->getMembers($ad_group) as $index) {
			$ad_user['login'] = strtolower($index['samaccountname'][0]);
			$ad_user['nome'] = $index['displayname'][0];
			
			if (isset($index['mail'][0]))
				$ad_user['email'] = strtolower($index['mail'][0]);
			else if (isset($index['userprincipalname'][0]))
				$ad_user['email'] = strtolower($index['userprincipalname'][0]);
			else 
				$ad_user['email'] = NULL;
			
			array_push($ad_users, $ad_user);
		}
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($ad_users)) );
	}
	
	private function sendmail($login, $tipo) {

		$this->load->model('ConfigsModel');
		$this->load->model('PessoasModel');

		$this->load->library('email');
		
		$this->email->initialize(array(
				'protocol' 	=> 'smtp',
				'smtp_host' => $this->ConfigsModel->getConfig('email_smtp'),
				'smtp_port' => 25,
				'mailtype'  => 'html'
		));
		
		$pessoa = $this->PessoasModel->getPessoa($login);
		$nome = explode(' ', $pessoa['nome']);
		
		$img = '/var/www/html/images/icone_v2.png';
		$this->email->attach($img);
		$cid = $this->email->attachment_cid($img);
		
		$body = array(
				1 => 'email_mensagem_novo',
				2 => 'email_mensagem_renovacao',
				3 => 'email_mensagem_kit'
		);
		
		$expiracao = explode('-', $this->UsuariosModel->getValidadeByLogin($login));
		$validade = $expiracao[2] .'/'. $expiracao[1] .'/'. $expiracao[0];
		
		$data = array(
				'nome' => $nome[0],
				'sobrenome' => end($nome),
				'cid' => $cid,
				'texto' => str_replace('{{login}}', $login, 
										str_replace( '{{validade}}', $validade, $this->ConfigsModel->getConfig( strtr($tipo, $body) ) )
						   )
		);
		
		$msg = $this->load->view('templates/email.php', $data, TRUE);
		
		$assunto = array(
				1 => 'Seu Kit ARES está disponível',
				2 => 'Renovação de Acesso',
				3 => 'Reenvio de Acesso'
		);
		
		if ($tipo == 2 || $tipo == 3)
			$this->email->attach('/home/ARES/'. $login .'/'. $login .'.pub');
		
		$this->email->from($this->ConfigsModel->getConfig('email_remetente'), 'Sistema ARES');
		$this->email->to( $pessoa['email'] );
		$this->email->reply_to('noreply@sefa.pa.gov.br');
		$this->email->subject( strtr($tipo, $assunto) );
		$this->email->message($msg);
		
		$this->email->send();
	}
}
