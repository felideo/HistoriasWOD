<?php
namespace Controller;

use Libs;

class Master extends \Framework\Controller {
	protected $modulo = [
		'modulo' 	=> 'master'
	];

	function logout() {
		$this->universe->session->destroy();
		header('location: /');
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

		echo shell_exec("sudo sh /www/Podkasto/automatic_deploy.sh");
		exit;
	}
}