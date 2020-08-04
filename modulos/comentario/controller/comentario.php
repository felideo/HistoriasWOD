<?php
namespace Controller;

use Libs;

class Comentario extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo'      => 'comentario',
		'name'        => 'Comentario',
		'table'       => 'comentario',
		'send'        => null,
		'localizador' => null,
		'seo'         => [
			'habilitado'    => false,
			'coluna'        => null,
			'robots_padrao' => null,
			'revise_padrao' => null,
		],
		// 'url'         => [
		// 	'coluna'    => null,
		// 	'metodo'    => null,
		// 	'atualizar' => false
		// ],
	];

	protected $datatable = [
		'colunas'                => ['ID', 'Nome', 'Ações'],
		'select'                 => ['id', 'nome'],
		'from'                   => 'comentario',
		'search'                 => ['id', 'nome'],
		'ordenacao_desabilitada' => '2'
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
				$item['nome'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function cadastrar_comentario($dados){
		$dados = carregar_variavel($this->modulo['modulo']);

		for($i = 0; $i < 10; $i++) {
			$dados['commentario'] = strip_tags($dados['commentario']);
		}

		$retorno = $this->insert_update($dados, []);

		$redirect = $this->model->query
			->select('url.url')
			->from('url url')
			->where("url.controller = '{$dados['controller']}' AND url.id_controller = '{$dados['id_controller']}' AND url.ativo = 1")
			->fetchArray('first');

		$this->redirect("/{$redirect['url']}");
	}
}