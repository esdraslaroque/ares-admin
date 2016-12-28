<?php
class RegrasModel extends CI_Model {
	public function __construct() {
		$this->load->database();
	}

	public function getRegras() {
		$this->db->select('regra.id, regra.descricao, destino, proto, servico, acao, pessoa_admin.login, grupo_regra_id,
						  (select descricao from grupo_regra where id = grupo_regra_id) as "grupo"');
		$this->db->from('regra');
		$this->db->join('admin','regra.admin_id = admin.id');
		$this->db->join('pessoa','admin.id = pessoa.id');
		$this->db->join('pessoa AS "pessoa_admin"','admin.id = pessoa_admin.id');
		
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	public function getGrupos() {
		$query = $this->db->get('grupo_regra');
		
		return $query->result_array();
	}

	public function setAcao($regra_id, $acao) {
		$data = array(
				'acao' => $acao
		);
	
		$this->db->where('id', $regra_id);
		$this->db->update('regra', $data);
	}

	public function removeRegra($regra_id) {
		$this->db->delete('permissao', array('regra_id' => $regra_id));
		$this->db->delete('regra', array('id' => $regra_id));
	}
	
	public function addRegra($destino, $proto, $servico, $descricao, $admin_id, $grupo_regra_id = NULL) {
		$data = array(
				'destino' => $destino,
				'proto' => $proto,
				'servico' => $servico,
				'acao' => 0,
				'descricao' => urldecode($descricao),
				'admin_id' => $admin_id,
				'grupo_regra_id' => $grupo_regra_id
		);
		
		$this->db->insert('regra', $data);
	}
	
	public function getRegraAdd($admin_id) {
		$this->db->select('regra.id, regra.descricao, destino, proto, servico, acao, pessoa_admin.login, grupo_regra_id,
						  (select descricao from grupo_regra where id = grupo_regra_id) as "grupo"');
		$this->db->from('regra');
		$this->db->join('admin','regra.admin_id = admin.id');
		$this->db->join('pessoa','admin.id = pessoa.id');
		$this->db->join('pessoa AS "pessoa_admin"','admin.id = pessoa_admin.id');
		$this->db->where('admin_id', $admin_id);
		$this->db->order_by('regra.id', 'DESC');
		$this->db->limit(1);
	
		$query = $this->db->get();
	
		return $query->row_array();
	}

	public function addGrupo($descricao) {
		$data = array('descricao' => urldecode($descricao) );
	
		$this->db->insert('grupo_regra', $data);
	}

	public function removeGrupo($grupo_id) {
		$this->db->select('id');
		$this->db->from('regra');
		$this->db->where('grupo_regra_id', $grupo_id);
		
		$query = $this->db->get();
		
		if ($query)
			foreach ($query->result() as $regra)
				$this->removeRegra($regra->id);
		
		$this->db->delete('grupo_regra', array('id' => $grupo_id));
	}

	public function editRegra($regra_id, $descricao, $destino, $proto, $servico, $admin_id, $grupo_regra_id = NULL) {
		/* Verificando UNIQUE KEYs */
		$this->db->select('id');
		$this->db->from('regra');
		$this->db->where('destino', $destino);
		$this->db->where('proto', $proto);
		$this->db->where('servico', $servico);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
				return FALSE;

		$data = array(
				'descricao' => urldecode($descricao),
				'destino' => urldecode($destino),
				'proto' => urldecode($proto),
				'servico' => $servico,
				'admin_id' => $admin_id,
				'grupo_regra_id' => $grupo_regra_id
		);
	
		$this->db->where('id', $regra_id);
		$this->db->update('regra', $data);
		
		if ($this->db->affected_rows() < 1)
			return FALSE;
		
		return TRUE;
	}
}