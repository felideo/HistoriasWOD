<?php
namespace Controller;

class Painel_Controle extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'painel_controle',
		'name'		=> 'Painel de Controle',
		'send'		=> 'Painel de Controle'
	];

	public function index(){
		$this->universe->auth->is_logged(true);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/painel_controle');
	}
}