<?php
namespace ControllerCore;

class Configuracao extends \Framework\ControllerCrud {
	Protected $modulo = [
		'modulo' 	=> 'configuracao',
		'name'		=> 'Configurações de Sistema',
		'send'		=> 'Configurações de Sistema'
	];

	public function listagem(){
		$this->universe->auth->is_logged(true);
		header('location: /' . $this->modulo['modulo'] . '/editar/1');
		exit;
	}

	public function middle_editar($id){
		$this->universe->auth->is_logged(true);
		parent::middle_editar($id);
		$this->view->assign('hierarquia_list', $this->model->load_active_list('hierarquia'));
	}
}