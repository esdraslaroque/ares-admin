<?php
class Ajuda extends CI_Controller {

	public function __construct() {
		parent::__construct ();
		$this->load->helper('indent_json');
		$this->load->helper('url_helper');
		$this->load->library('session');
		
		if (! $this->session->admin_username)
			redirect(base_url('/app/admin'));
	}
	
	public function view ($page = NULL) {
		
		if (empty($page))
			show_error('Parameter Needed');
		
		if (! file_exists ( APPPATH . 'views/ajuda/' . $page . '.php' )) {
			show_404();
		}
		
		$this->load->view ('ajuda/' . $page);
	}
	
	public function list_manuais () {
		$files = scandir (APPPATH . 'views/ajuda/');
		
		/* Escapando os diretorios de retorno ".." */
		array_shift($files); array_shift($files);
		
		$manuais = array();
		
		foreach ($files as $ajuda) {
			$tags = get_meta_tags(APPPATH . 'views/ajuda/' . $ajuda);
			$indice = explode('.', $ajuda);
			
			$index['indice'] = $indice[0];
			$index['pagina'] = $ajuda;
			$index['titulo'] = $tags['title'];
			
			array_push($manuais, $index);
		}
		
		$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($manuais)) );
	}
}