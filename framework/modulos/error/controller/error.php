<?php
namespace Controller;

class Error extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'error',
		'name'		=> 'Error',
		'send'		=> 'Error'
	];

	public function index($url_error){
		http_response_code (404);

		if(defined('DEVELOPER') && !empty(DEVELOPER)){
			debug2($url_error);
		}

		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/error/error');
	}
}