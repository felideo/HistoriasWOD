<?php
namespace ControllerCore;

class pagina_institucional extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' => 'pagina_institucional',
		'name'   => 'Paginas Institucionais',
		'send'   => 'Pagina Institucional',
		'url'    => [
			'url'    => 'titulo',
			'metodo' => 'visualizar_front'
		],
		'html_cloud_editor_column' => 'conteudo'
	];

	protected $datatable = [
		'colunas' => ['ID <i class="fa fa-search"></i>', 'Titulo <i class="fa fa-search"></i>', 'Ações'],
		'select'  => ['id', 'titulo'],
		'from'    => 'pagina_institucional',
		'search'  => ['id', 'titulo'],
		'ordenacao_desabilitada' => '2'
	];

	protected function carregar_dados_listagem_ajax($busca){
		$this->universe->auth->is_logged(true);
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query['dados'] as $indice => $item) {
			$retorno['dados'][] = [
				$item['id'],
				$item['titulo'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		$retorno['total'] = $query['total'];
		return $retorno;
	}

	public function carregar_conteudo($id){
		$this->check_if_exists($id);
		$this->view->assign('cadastro', $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0]);
	}

	public function visualizar_front($id){
		$this->universe->auth->is_logged(true);
		$cadastro         = $this->carregar_conteudo($id[0]);
		$front_controller = $this->carregar_front();

		// $this->view->render('front/cabecalho_rodape', $this->modulo['modulo'] . '/view/front/front');
		$this->view->render_plataforma('header', 'footer', 'pagina_institucional');
	}

	public function load_source_code_ajax(){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "editar");
		// $this->check_if_exists($parametros[0], 'plataforma_pagina');

		$parametros = carregar_variavel('data');
		echo json_encode($this->model->full_load_by_id($this->modulo['modulo'], $parametros['id'])[0]);
		exit;
	}

	public function save_source_code_ajax($id){
		$this->universe->auth->is_logged(true);
		$update_db[$this->modulo['html_cloud_editor_column']] = $_POST['data'];

		$retorno = $this->model->update($this->modulo['modulo'], $update_db, ['id' => $id[0]]);

		echo json_encode(!empty($retorno['status']));
		exit;
	}
}

