<?php
namespace Controller;

use Libs;

class Livro extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo'      => 'livro',
		'name'        => 'livro',
		'table'       => 'livro',
		'send'        => null,
		'localizador' => null,
		'url'         => [
			'coluna'    => null,
			'metodo'    => null,
			'atualizar' => false
		],
	];

	protected $datatable = [
		'colunas'                => ['ID', 'Livro', 'Ano', 'Ações'],
		'select'                 => ['id', 'titulo', 'ano'],
		'from'                   => 'livro',
		'search'                 => ['id', 'titulo', 'ano'],
		'ordenacao_desabilitada' => '3'
	];

	protected function carregar_dados_listagem_ajax($busca){
		$busca['length'] = 1000;
		$busca['limit']  = 100;
		$query           = $this->model->carregar_listagem($busca, $this->datatable);
		$retorno         = [
			'dados' => [],
			'total' => $query['total']
		];

		foreach ($query['dados'] as $indice => $item) {
			$retorno['dados'][] = [
				$item['id'],
				$item['titulo'],
				$item['ano'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}
}