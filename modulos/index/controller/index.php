<?php
namespace Controller;

class Index extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'index',
		'name'		=> 'Index',
		'send'		=> 'Index'
	];

	public function index(){
		$front_controller = $this->get_controller('front');
		$front_controller->carregar_cabecalho_rodape();
		$this->view->render_plataforma('index');
	}
}