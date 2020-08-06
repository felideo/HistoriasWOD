<?php
namespace ControllerCore;

class Configuracao extends \Framework\ControllerCrud {
	protected $modulo = [
		'modulo'      => 'configuracao',
		'name'        => 'Configurações de Sistema',
		'table'       => 'configuracao',
		'send'        => 'Configurações de Sistema',
		'localizador' => null,
		'seo'         => [
			'habilitado'    => true,
			'title_padrao'  => 'aplicacao_nome',
			'robots_padrao' => 'index, follow',
			'revise_padrao' => '2 days',
		],
		'url'         => [
			'coluna'    => null,
			'metodo'    => null,
			'atualizar' => false
		],
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