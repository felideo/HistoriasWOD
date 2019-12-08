<?php
namespace ControllerCore;

use Libs;

class Banner extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'banner',
		'name'		=> 'Banners',
		'send'		=> 'Banner'
	];

	protected $datatable = [
		'colunas' => ['ID', 'Ordem <i class="fa fa-search"></i>', 'Imagem <i class="fa fa-search"></i>', 'Ações'],
		'select'  => ['id', 'ordem', 'id_arquivo'],
		'from'    => 'banner',
		'search'  => ['id', 'ordem', 'id_arquivo'],
		'ordenacao_desabilitada' => '3'

	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		if(empty($query)){
			return $retorno;
		}

		foreach ($query['dados'] as $indice => $item) {
			$retorno['dados'][] = [
				$item['id'],
				$item['ordem'],
				$item['arquivo'][0]['nome'],
				$this->view->default_buttons_listagem($item['id'], true, false, true)
			];
		}

		$retorno['total'] = $query['total'];
		return $retorno;
	}

	public function visualizar($id){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$this->view->assign('cadastro', $this->model->carregar_banner($id[0])[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}
}