<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UsuariosModel extends CI_Model {
	public function __construct() {
		$this->load->database();
	}

	public function getUsuarios($login = FALSE) {
		$this->db->select('usuario.ativo, usuario.id, usuario.criado, usuario.home, usuario.id_key, usuario.expedida,
						   usuario.validade, usuario.processo, pessoa.login, pessoa.nome, pessoa.email, pessoa_admin.login as "admin",
						   (select count(id) from admin where admin.id = usuario.id) as "isadmin",
						   (select count(id) from conexao where conexao.usuario_id = usuario.id and autenticado = 1 and fim is null) as "isconect"');
		$this->db->from('usuario');
		$this->db->join('pessoa','usuario.id = pessoa.id');
		$this->db->join('admin','usuario.admin_id = admin.id');
		$this->db->join('pessoa AS "pessoa_admin"','admin.id = pessoa_admin.id');
		
		if ($login)
			$this->db->where('pessoa.login', $login);

		$query = $this->db->get();

		if ($login)
			return $query->row_array();
		
		return $query->result_array();
	}
	
	public function getRegras() {
		$this->db->select('id, descricao');
		$this->db->from('regra');
		
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	public function getHome($usuario_id = FALSE, $login = FALSE) {
		
		if (!$usuario_id && !$login)
			show_error('Parameter Needed');
		
		$this->db->select('home');
		$this->db->from('usuario');

		if ($login) {
			$this->db->join('pessoa','usuario.id = pessoa.id');
			$this->db->where('login', $login);
		} else {
			$this->db->where('id', $usuario_id);
		}
		
		$query = $this->db->get();
		
		return $query->row_array();
	}

	public function setAtivo($usuario_id, $ativo) {
		$data = array(
				'ativo' => $ativo
		);
		
		$this->db->where('id', $usuario_id);
		$this->db->update('usuario', $data);
	}
	
	public function updateAdmin($admin_id, $usuario_id) {
		$data = array(
				'admin_id' => $admin_id
		);
		
		$this->db->where('id', $usuario_id);
		$this->db->update('usuario', $data);
	}

	public function updateProcesso($processo, $usuario_id) {
		$data = array(
				'processo' => $processo
		);
	
		$this->db->where('id', $usuario_id);
		$this->db->update('usuario', $data);
	}
	
	public function removeUsuario($usuario_id) {
		$this->db->delete('permissao', array('usuario_id' => $usuario_id));
		$this->db->delete('usuario', array('id' => $usuario_id));
	}
	
	public function getConInfo($usuario_id) {
		$this->db->select('interface, ip_cliente, pid, inicio');
		$this->db->from('conexao');
		$this->db->where('usuario_id', $usuario_id);
		$this->db->where('fim', NULL);
		$this->db->order_by('inicio', 'DESC');
		$this->db->limit(1);

		$query = $this->db->get();
		
		return $query->row_array();
	}

	public function addUsuario($login, $ativo, $pessoa_id, $processo, $admin_id) {
		$data = array(
				'ativo' => $ativo,
				'home' => '/home/ARES/'. $login,
				'id_key' => 'XXXXXXXXXXXXXXXX',
				'id' => $pessoa_id,
				'processo' => $processo,
				'admin_id' => $admin_id
		);
		
		$this->db->insert('usuario', $data);
	}

	public function getValidadeByLogin($login) {
		$this->db->select('validade');
		$this->db->from('usuario');
		$where = "id = (select id from pessoa where login = '". $login ."')";
		$this->db->where($where);
		
		$query = $this->db->get();
		return $query->row()->validade;
	}
	
	public function getUsersExpireIn($dias, $aviso) {
		$this->db->select('pessoa.id, pessoa.nome, pessoa.login, pessoa.email, usuario.validade');
		$this->db->from('pessoa');
		$this->db->join('usuario', 'usuario.id = pessoa.id');
		$where = "pessoa.id in (select id from usuario where aviso = ". $aviso ." and validade > date() AND validade <= date('now', '+". $dias ."days'))";
		$this->db->where($where);
		
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	public function setAviso($usuario_id, $n) {
		if ($n == 0 || $n == 1 || $n == 2) {

			$data = array('aviso' => $n);
			
			$this->db->where('id', $usuario_id);
			$this->db->update('usuario', $data);
			
		} else 
			show_error('Failed parameter..');		
	}

	public function checkProcesso($processo) {
		
		/* 
		 * ************************** IMPORTANTE ******************************** 
		 * Retornando sempre falso para desativar verificar de processo duplicado 
		 */
		return FALSE;
		
		$this->db->select('processo');
		$this->db->from('usuario');
		$this->db->where('processo', $processo);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
}