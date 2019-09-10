<?php
namespace Controller;

use Libs;

class Idioma extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo'      => 'idioma',
		'name'        => 'Idioma',
		'send'        => 'Idioma'
		'localizador' => 'idioma'
		'table'       => 'idioma'
		'url'         => false
	];

	protected $colunas = ['ID', 'Idioma', 'Ações'];

	protected $datatable = [
		'colunas' => ['ID', 'Idioma', 'Ações'],
		'select'  => ['id', 'nome', 'email', 'link'],
		'from'    => 'autor',
		'search'  => ['id', 'nome', 'email', 'link']
	];

	public function carregar_listagem_ajax(){
		$busca = [
			'order'  => carregar_variavel('order'),
			'search' => carregar_variavel('search'),
			'start'  => carregar_variavel('start'),
			'length' => carregar_variavel('length'),
		];

		$query = $this->model->carregar_listagem($busca);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['idioma'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		echo json_encode([
            "draw"            => intval(carregar_variavel('draw')),
            "recordsTotal"    => intval(count($retorno)),
            "recordsFiltered" => intval($this->model->select("SELECT count(id) AS total FROM {$this->modulo['modulo']} WHERE ativo = 1")[0]['total']),
            "data"            => $retorno
        ]);

		exit;
	}

	public function buscar_idioma_select2(){
		$busca = carregar_variavel('busca');

		$retorno = $this->model->buscar_idioma($busca);

		if(isset($busca['cadastrar_busca']) && !empty($busca['cadastrar_busca']) && $busca['cadastrar_busca'] == 'true' && $busca['nome'] != '%%'){
			$add_cadastro[0] = [
				'id'               => $busca['nome'],
				'idioma'             => "<strong>Cadastrar Novo Idioma: </strong>" . $busca['nome'],
			];

			$retorno = array_merge($add_cadastro, $retorno);
		}

		echo json_encode($retorno);
		exit;
	}
}