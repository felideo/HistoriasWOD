<?php
namespace ControllerCore;

class Master extends \Framework\Controller {
	protected $modulo = [
		'modulo' 	=> 'master'
	];

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

		$commands = [
			"echo $PWD",
			"whoami",
			"git checkout -- .",
			"git fetch --all",
			"git status",
			"git reset --hard origin/master",
			"git pull",
			"git log -5 --pretty=format:'%h - %cn (%ce) - %s (%ci)'",
		];

		$retorno = '';

		foreach($commands AS $command){
			$tmp = shell_exec($command);

			$retorno .= "<span style=\"color: #6BE234;\">\$</span><span style=\"color: #729FCF;\">{$command}\n</span><br />";
			$retorno .= htmlentities(trim($tmp)) . "\n<br /><br />";
		}

		$this->view->assign('retorno', $retorno);
		$this->view->render(false, $this->modulo['modulo'] . '/view/deploy');
		exit;
	}

}