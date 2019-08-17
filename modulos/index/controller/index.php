<?php
namespace Controller;

class Index extends \Framework\Controller {
	protected $modulo = [
		'modulo' 	=> 'index',
	];

	public function index($parametros = false){
		$this->view->render(null, $this->modulo['modulo'] . '/view/index');
	}
}