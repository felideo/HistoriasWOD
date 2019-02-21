<?php
namespace Controller;

use Libs;

class Master extends \Framework\Controller {
	protected $modulo = [
		'modulo' 	=> 'master'
	];

	function index(){
	}

	function logout() {
		\Libs\Session::destroy();
		header('location: /index');
		exit;
	}

	function limpar_alertas_ajax(){
		unset($_SESSION['alertas']);
		echo json_encode("Alertas limpos");
		exit;
	}

	function limpar_notificacoes_ajax(){
		unset($_SESSION['notificacoes']);
		echo json_encode("Notificacoes limpas");
		exit;
	}

	public function deploy($parametros){
		if($parametros[0] != DEPLOY_KEY){
			header('location: /');
			exit;
		}

		echo shell_exec("sudo sh /www/swdb/automatic_deploy.sh");
		exit;
	}

	public function index_es(){
		$elastic_search = new \Libs\ElasticSearch\ElasticSearch();
		$elastic_search->criar_intex();
	}

	public function sudo_service_elastic_search_restart(){
		echo shell_exec("sudo sh /www/swdb/sudo_service_elastic_search_restart.sh");
		exit;
	}
}