<?php
namespace Controller;

use Libs;

class Configuracao extends \Framework\ControllerCrud {

	Protected $modulo = [
		'modulo' 	=> 'configuracao',
		'name'		=> 'Configurações de Sistema',
		'send'		=> 'Configurações de Sistema'
	];

	public function index(){
		header('location: /' . $this->modulo['modulo'] . '/editar/1');
		exit;
	}

}