<?php
namespace Controller;

use Libs;

class Produto extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'produto',
		'name'		=> 'Produtos',
		'send'		=> 'produto'
	];

	protected $datatable = [
		'colunas' => ['ID <i class="fa fa-search"></i>', 'Nome <i class="fa fa-search"></i>', 'Preco', 'Link', 'Ações'],
		'select'  => ['id', 'nome', 'preco', 'link'],
		'from'    => 'produto',
		'search'  => ['id', 'nome'],
		'ordenacao_desabilitada' => '4'

	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		if(empty($query)){
			return $retorno;
		}

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['nome'],
				$item['preco'],
				$item['link'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function middle_visualizar($id){
		$this->view->assign('cadastro', $this->model->carregar_cadastro($id[0])[0]);
	}

	public function middle_editar($id){
		$this->view->assign('cadastro', $this->model->carregar_cadastro($id[0])[0]);
	}
}