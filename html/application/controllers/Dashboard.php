<?php
class Dashboard extends CI_Controller {
    public function __construct() {
        parent::__construct ();
        $this->load->model('DashboardModel');
        $this->load->helper('indent_json');
        $this->load->helper('url_helper');
		$this->load->library('session');
		
		if (! $this->session->admin_username)
			redirect(base_url('/app/admin'));
    }

    public function index()
        {
            $user_ativos_geral = $this->DashboardModel->get_user_ativos_geral();
            $total_bytes = $this->DashboardModel->total_bytes_mes();

            $qtd_user_conect = $this->DashboardModel->get_qtd_user_conect();
            $qtd_user_conect_mes = $this->DashboardModel->get_qtd_user_conect_mes();
            $qtd_15day = $this->DashboardModel->get_qtd_15day();
    //TELA PRINCIPAL
            $consulta_geral['acesso_online'] = $qtd_user_conect;
            $consulta_geral['qtd_15day'] = $qtd_15day;
            $consulta_geral['qtd_user_conect_mes'] = $qtd_user_conect_mes;
            $consulta_geral['ativo'] = $user_ativos_geral['Ativo'];
            $consulta_geral['expirado'] = $user_ativos_geral['Expirados'];
            $consulta_geral['inativo'] = $user_ativos_geral['Inativo'];
            $consulta_geral['bytes_in'] = $total_bytes['bytes_in'];
            $consulta_geral['bytes_out'] = $total_bytes['bytes_out'];
            $consulta_geral['bytes_total'] = $total_bytes['bytes_in'] + $total_bytes['bytes_out'];

            $this->output->set_content_type('application/json')->set_output( indent_json(json_encode($consulta_geral)) );

        }
    public function validos()
        {
            $user_validos = $this->DashboardModel->get_user_validos();
            $this->output->set_content_type('application/json')->set_output( indent_json(json_encode($user_validos)) );

        }
    public function inativos()
        {
            $user_inativos = $this->DashboardModel->get_user_inativos();

            $this->output->set_content_type('application/json')->set_output( indent_json(json_encode($user_inativos)) );

        }
    public function Expirados()
        {
            $user_expirados = $this->DashboardModel->get_user_expirados();

            $this->output->set_content_type('application/json')->set_output( indent_json(json_encode($user_expirados)) );

        }

	public function conexao_estatistica ($nivel, $mes = NULL, $ano = NULL) {

		if ($nivel == 1) {
		/* Nivel para recuperar acessos por mês */
			$mes_br = array(
					'Feb'	=> 'Fev',
					'Apr'	=> 'Abr',
					'May'	=> 'Mai',
					'Aug'	=> 'Ago',
					'Sep'	=> 'Set',
					'Oct'	=> 'Out',
					'Dec'	=> 'Dez'
			);
			$meses = array();
			$acessos = array();

			array_push($meses, strtr(date('M/Y'), $mes_br));
			array_push($acessos, $this->DashboardModel->total_acessos_mes(NULL));

			for ($i = 1; $i < 6; $i++) {
				array_push($meses, strtr(date('M/Y', strtotime('-'. $i .' month')), $mes_br) );
				array_push($acessos, $this->DashboardModel->total_acessos_mes($i));
			}

			$dados['labels'] = $meses;
			$dados['dados'] = $acessos;

			$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($dados)) );

		} else if ($nivel == 2) {
		/* Nivel para recuperar acessos por dia */
			if (empty($mes) || empty($ano))
				return;

			$mes_num = array(
					'Jan' => '01',
					'Fev' => '02',
					'Mar' => '03',
					'Abr' => '04',
					'Mai' => '05',
					'Jun' => '06',
					'Jul' => '07',
					'Ago' => '08',
					'Set' => '09',
					'Out' => '10',
					'Nov' => '11',
					'Dez' => '12'
			);

			$dias = array();
			$acessos = array();

			$start_date = "01-".strtr($mes, $mes_num)."-".$ano;
			$start_time = strtotime($start_date);
			$end_time = strtotime("+1 month", $start_time);

			for($i=$start_time; $i<$end_time; $i+=86400) {
				array_push($dias, date('d', $i));
				array_push($acessos, $this->DashboardModel->total_acessos_dia(date('Y-m-d', $i)));
			}

			$dados['refer'] = $ano.'-'.strtr($mes, $mes_num);
			$dados['labels'] = $dias;
			$dados['dados'] = $acessos;

			$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($dados)) );

		} else if ($nivel == 3) {
		/* Nivel para recuperar acessos por hora */
			$horas = array();
			$acessos = array();

			$dia = $mes .'-'. $ano;

			for ($i=0; $i<24; $i++) {
				array_push($horas, sprintf('%dh', $i));
				array_push($acessos, $this->DashboardModel->total_acessos_dia($dia, sprintf('%02d', $i)));
			}

			$dados['labels'] = $horas;
			$dados['dados'] = $acessos;

			$this->output->set_content_type('application/json')->set_output( indent_json(json_encode($dados)) );

		} else
			return;
	}

	public function trafego_rede () {
		$output = file_get_contents('http://127.0.0.1/troughput');
		$this->output->set_content_type('application/json')->set_output( $output );
	}

// 	/*
// 	 * Metodo para gerar dados aleatórios na tabela de conexao
// 	 * (Para testes)
// 	 */
// 	public function gera_dados_conexao() {
// 		$min = strtotime(date('Y-m-d H:i:s',strtotime('-6 month')));
// 		$max = strtotime(date('Y-m-d H:i:s'));

// 		$val = rand($min, $max);

// 		$randate = date('Y-m-d H:i:s', $val);

// 		$insert = 'insert into conexao (interface, ip_cliente, inicio, pid, bytes_in, bytes_out, usuario_id, autenticado)
// 				           values ("ppp'.rand(0,10).'", "172.16.42.3'.rand(0,9).'", "'.$randate.'", '.rand(876, 33467).',
// 				           		   '.rand(1024,65535).', '.rand(1024,65535).', '.rand(2,55).', '.rand(0,1).')';
// 		$update = 'update conexao set fim = (select strftime("%Y-%m-%d %H:%M:%S",(select inicio from conexao order by id desc limit 1), "+10 minutes"))
// 				     where id = (select id from conexao order by id desc limit 1)';
//  		$this->load->database();

//  		$this->db->query($insert);
//  		$this->db->query($update);
// 	}


    public function ares_estatistica ($nivel, $mes = NULL, $ano = NULL) {

        if ($nivel == 1) {
        /* Nivel para recuperar acessos por mês */
            $mes_br = array(
                    'Feb'   => 'Fev',
                    'Apr'   => 'Abr',
                    'May'   => 'Mai',
                    'Aug'   => 'Ago',
                    'Sep'   => 'Set',
                    'Oct'   => 'Out',
                    'Dec'   => 'Dez'
            );
            $meses = array();
            $acessos = array();

            array_push($meses, strtr(date('M/Y'), $mes_br));
            array_push($acessos, $this->DashboardModel->total_ares_mes(NULL));

            for ($i = 1; $i < 6; $i++) {
                array_push($meses, strtr(date('M/Y', strtotime('-'. $i .' month')), $mes_br) );
                array_push($acessos, $this->DashboardModel->total_ares_mes($i));
            }

            $dados['labels'] = $meses;
            $dados['dados'] = $acessos;

            $this->output->set_content_type('application/json')->set_output( indent_json(json_encode($dados)) );

        } else if ($nivel == 2) {
        /* Nivel para recuperar acessos por dia */
            if (empty($mes) || empty($ano))
                return;

            $mes_num = array(
                    'Jan' => '01',
                    'Fev' => '02',
                    'Mar' => '03',
                    'Abr' => '04',
                    'Mai' => '05',
                    'Jun' => '06',
                    'Jul' => '07',
                    'Ago' => '08',
                    'Set' => '09',
                    'Out' => '10',
                    'Nov' => '11',
                    'Dez' => '12'
            );

            $dias = array();
            $acessos = array();

            $start_date = "01-".strtr($mes, $mes_num)."-".$ano;
            $start_time = strtotime($start_date);
            $end_time = strtotime("+1 month", $start_time);

            for($i=$start_time; $i<$end_time; $i+=86400) {
                array_push($dias, date('d', $i));
                array_push($acessos, $this->DashboardModel->total_ares_dia(date('Y-m-d', $i)));
            }

            $dados['refer'] = $ano.'-'.strtr($mes, $mes_num);
            $dados['labels'] = $dias;
            $dados['dados'] = $acessos;

            $this->output->set_content_type('application/json')->set_output( indent_json(json_encode($dados)) );

        } else if ($nivel == 3) {
        /* Nivel para recuperar acessos por hora */
            $horas = array();
            $acessos = array();

            $dia = $mes .'-'. $ano;

            for ($i=0; $i<24; $i++) {
                array_push($horas, sprintf('%dh', $i));
                array_push($acessos, $this->DashboardModel->total_ares_dia($dia, sprintf('%02d', $i)));
            }

            $dados['labels'] = $horas;
            $dados['dados'] = $acessos;

            $this->output->set_content_type('application/json')->set_output( indent_json(json_encode($dados)) );

        } else
            return;
    }

}