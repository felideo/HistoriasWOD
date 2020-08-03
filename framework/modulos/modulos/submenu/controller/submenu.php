<?php
namespace ControllerCore;

class Submenu extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'submenu',
	];

	protected $datatable = [
		'colunas' => ['ID', 'Submenu', 'Icone', 'Ações'],
		'select'  => ['id', 'nome', 'icone', 'nome_exibicao'],
		'from'    => 'submenu',
		'search'  => ['id', 'nome_exibicao', 'icone']
	];

	protected function carregar_dados_listagem_ajax($busca){
		$this->universe->auth->is_logged(true);
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query['dados'] as $indice => $item) {
			$retorno['dados'][] = [
				$item['id'],
				$item['nome_exibicao'],
				$item['icone'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		$retorno['total'] = $query['total'];

		return $retorno;
	}
}