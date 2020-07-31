<?php
namespace Controller;

class Index extends \Framework\Controller {

	protected $modulo = [
		'modulo'      => 'index',
		'name'        => 'Index',
		'table'       => 'Index',
		'send'        => null,
		'localizador' => null,
		'url'         => [
			'coluna'    => null,
			'metodo'    => null,
			'atualizar' => false
		],
	];

	public function index($parametros){
		$this->view->assign('posts', $this->universe->get_model('post')->carregar_post());
		$this->view->assign('livros', $this->universe->get_model('livro')->carregar_livro());
		$this->view->render_plataforma('', '', 'index', ['site_cabecalho', 'site_rodape', 'post_item']);
	}
}