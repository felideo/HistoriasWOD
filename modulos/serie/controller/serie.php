<?php
namespace Controller;

class Serie extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'serie',
	];

	protected $datatable = [
		'colunas'                => ['ID', 'Serie', 'Ações'],
		'select'                 => ['id', 'serie'],
		'from'                   => 'serie',
		'search'                 => ['id', 'serie'],
		'ordenacao_desabilitada' => ''
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
				$item['serie'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}
}