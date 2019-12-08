<?php
namespace ControllerCore;

use Libs;

class Configuracao extends \Framework\ControllerCrud {

	Protected $modulo = [
		'modulo' 	=> 'configuracao',
		'name'		=> 'Configurações de Sistema',
		'send'		=> 'Configurações de Sistema'
	];

	public function listagem(){
		header('location: /' . $this->modulo['modulo'] . '/editar/1');
		exit;
	}

	public function middle_editar($id) {
		parent::middle_editar($id);
		$this->view->assign('hierarquia_list', $this->model->load_active_list('hierarquia'));
	}

}