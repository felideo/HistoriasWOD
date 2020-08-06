<?php
namespace Controller;

class Livro extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo'      => 'livro',
		'name'        => 'livro',
		'table'       => 'livro',
		'send'        => 'livro',
		'localizador' => null,
		'seo'         => [
			'habilitado'    => true,
			'title_padrao'  => 'titulo',
			'robots_padrao' => 'index, follow',
			'revise_padrao' => '2 days',
		],
		'url'         => [
			'coluna'    => 'titulo',
			'metodo'    => 'exibir_front',
			'atualizar' => true
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

	public function exibir_front($parametros){

	}
}