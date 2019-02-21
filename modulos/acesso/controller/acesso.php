<?php
namespace Controller;

class Acesso extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'acesso',
		'name'		=> 'Acesso',
		'send'		=> 'Acesso'
	];

	public function admin($parametros){
		if(\Libs\Auth::esta_logado()){
			header('location: /painel_controle');
			exit;
		}

		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/back/login');
	}

	public function run_back(){
		if($this->model->run_back(carregar_variavel('acesso'))){
			header('location: /painel_controle');
			exit;
		}

		$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
		header('location: ' . \Libs\Redirect::getUrl());
		exit;
	}
}

