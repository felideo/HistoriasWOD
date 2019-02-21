<?php
namespace Controller;

use Libs;

class Pagina_institucional extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' => 'pagina_institucional',
		'name'   => 'Paginas Institucionais',
		'send'   => 'Pagina Institucional',
		'url'    => [
			'url'    => 'titulo',
			'metodo' => 'visualizar_front'
		]
	];

	protected $datatable = [
		'colunas' => ['ID <i class="fa fa-search"></i>', 'Titulo <i class="fa fa-search"></i>', 'Ações'],
		'select'  => ['id', 'titulo'],
		'from'    => 'pagina_institucional',
		'search'  => ['id', 'titulo'],
		'ordenacao_desabilitada' => '2'
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['titulo'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function visualizar_front($id){
		$this->check_if_exists($id[0]);

		$front_controller = $this->get_controller('front');
		$front_controller->carregar_cabecalho_rodape();

		$this->get_controller('contador')->contar('visita');

		$cadastro = $this->model->full_load_by_id($this->modulo['modulo'], $id[0])[0];

		$this->view->assign('cadastro', $cadastro);
		// $this->view->render('front/cabecalho_rodape', $this->modulo['modulo'] . '/view/front/front');
		$this->view->render_plataforma('pagina_institucional');

	}
}