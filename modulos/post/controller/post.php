<?php
namespace Controller;

class Post extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo'      => 'post',
		'name'        => 'Post',
		'table'       => 'post',
		'send'        => 'post',
		'localizador' => null,
		'seo'         => [
			'habilitado'    => true,
			'title_padrao'  => 'titulo',
			'robots_padrao' => 'index, follow',
			'revise_padrao' => '2 days',
		],
		'url'         => [
			'coluna'    => 'titulo',
			'metodo'    => 'exibir_post',
			'atualizar' => true
		],
	];

	protected $datatable = [
		'colunas'                => ['ID', 'Titulo', 'Ações'],
		'select'                 => ['id', 'titulo'],
		'from'                   => 'post',
		'search'                 => ['id', 'titulo'],
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
				$item['titulo'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function middle_index(){
		$series = $this->model->load_active_list('serie', 'id, serie');
		$this->view->assign('series', $this->model->load_active_list('serie', 'id, serie'));
		$this->view->assign('livros', $this->model->load_active_list('livro', 'id, titulo, titulo_original'));
	}

	public function middle_editar($id){
		parent::middle_editar($id);

		$this->view->assign('series', $this->model->load_active_list('serie', 'id, serie'));
		$this->view->assign('livros', $this->model->load_active_list('livro', 'id, titulo, titulo_original'));
	}

	public function insert_update($dados, $where){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		$retorno = $this->model->insert_update(
			$table,
			$where,
			$dados,
			true
		);

		if(!empty($retorno['id'])){
			$retorno_url = (new \Libs\URL)->setId($retorno['id'])
				->setUrl($dados['titulo'])
				->setController($this->modulo['modulo'])
				->setMetodo('exibir_post')
				->atualizar(true)
				->caseSensitive(false)
				->cadastrarUrlAmigavel();
		}

		return $retorno;
	}

	public function exibir_post($parametros){
		$post  = $this->model->carregar_post($parametros[0]);

		$this->view->assign('cadastro', $post[0]);
		$this->view->assign('posts', $this->universe->get_model('post')->carregar_post());
		$this->view->assign('livros', $this->universe->get_model('livro')->carregar_livro());
		$this->view->render_plataforma('', '', 'post', ['site_cabecalho', 'site_rodape', 'sidebar', 'menu', 'seo']);
	}
}