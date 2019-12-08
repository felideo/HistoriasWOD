<?php
namespace ControllerCore;

use Libs;

class Permissao extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'permissao',
		'name'		=> 'Permissões',
		'send'		=> 'Permissão'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Modulo', 'Permissão', 'Ações'],
		'select'  => ['id', 'id_modulo', 'permissao'],
		'from'    => 'permissao',
		'search'  => ['id', 'id_modulo', 'permissao']
	];

	public function listagem() {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->view->assign('permissao_criar', $this->universe->permission->check_user_permission($this->modulo['modulo'], 'criar'));

		if(isset($this->datatable) && !empty($this->datatable)){
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$this->view->assign('modulos', $this->model->load_active_list('modulo'));
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	public function editar($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$this->view->assign('modulos', $this->model->load_active_list('modulo'));
		$this->view->assign('cadastro', $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0]);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function visualizar($id){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$this->view->assign('modulos', $this->model->load_active_list('modulo'));
		$this->view->assign('cadastro', $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query['dados'] as $indice => $item) {
			$retorno['dados'][] = [
				$item['id'],
				$item['modulo'][0]['nome'],
				$item['permissao'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		$retorno['total'] = $query['total'];
		return $retorno;
	}
}