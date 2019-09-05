<?php
namespace Controller;

class Ideia extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'ideia',
	];

	protected $datatable = [
		'colunas'                => ['ID', 'Nome' ,'Descrição', 'Ações'],
		'select'                 => ['id', 'nome', 'descricao'],
		'from'                   => 'ideia',
		'search'                 => ['id', 'nome'],
		'ordenacao_desabilitada' => '2, 3'
	];

	protected function carregar_dados_listagem_ajax($busca){
		$busca['length'] = 1000;
		$busca['limit']  = 100;
		$query           = $this->model->carregar_listagem($busca, $this->datatable);
		$retorno         = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['nome'],
				$item['descricao'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}
}