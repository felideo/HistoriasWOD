<?php
namespace Controller;

use Libs;

class Venda extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'venda',
		'name'		=> 'Vendas',
		'send'		=> 'venda'
	];

	protected $datatable = [
		'colunas'                => ['ID', 'Pessoa', 'Produto', 'Valor', 'Status', 'Ações'],
		'from'                   => 'banner',
		'ordenacao_desabilitada' => '1, 2, 3 ,4, 5'
	];

	private $status = [
		1 => 'Aguardando pagamento',
		2 => 'Em análise',
		3 => 'Paga',
		4 => 'Disponível',
		5 => 'Em disputa',
		6 => 'Devolvida',
		7 => 'Cancelada',
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
				$item['usuario'][0]['pessoa'][0]['nome'] . ' ' . $item['usuario'][0]['pessoa'][0]['sobrenome'],
				$item['produto'][0]['nome'],
				number_format($item['produto'][0]['preco'], 2, ',', '.'),
				$this->status[$item['status']],
				$this->view->default_buttons_listagem($item['id'], true, false, false)
			];
		}

		ob_clean();

		return $retorno;
	}

	public function visualizar($id){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$this->view->assign('cadastro', $this->model->carregar_banner($id[0])[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}
}