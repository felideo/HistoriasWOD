<?php
namespace ControllerCore;

use Libs;

class Url extends \Framework\ControllerCrud {
	private $hierarquia_organizada = [];

	protected $modulo = [
		'modulo'            => 'url',
		'name'              => 'URL',
		'delete_message' => 'Tem certeza que deseja deletar esta URL?'
	];

	protected $datatable = [
		'colunas'                => ['ID', 'URL  <i class="fa fa-search"></i>', 'ID Controller <i class="fa fa-search"></i>', 'Controller  <i class="fa fa-search"></i>', 'Método <i class="fa fa-search"></i>', 'Ações'],
		'select'                 => ['id', 'url', 'id_controller', 'controller', 'metodo'],
		'from'                  => 'url',
		'search'                 => ['url', 'id_controller', 'controller', 'metodo'],
		'ordenacao_desabilitada' => '3'
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		if(empty($query)){
			$query = [];
		}

		foreach($query['dados'] as $indice => $item){
			$retorno['dados'][] = [
				$item['id'],
				$item['url'],
				$item['id_controller'],
				$item['controller'],
				$item['metodo'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		$retorno['total'] = $query['total'];
		return $retorno;
	}
}