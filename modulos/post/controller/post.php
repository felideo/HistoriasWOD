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
		'colunas'                => ['ID', 'Titulo', 'Livro', 'Ações'],
		'select'                 => ['id', 'titulo', 'id_livro'],
		'from'                   => 'post',
		'search'                 => ['id', 'titulo', 'id_livro'],
		'ordenacao_desabilitada' => ''
	];

	protected function carregar_dados_listagem_ajax($busca){
		// $busca['length'] = 1000;
		// $busca['limit']  = 100;
		$query           = $this->model->carregar_listagem($busca, $this->datatable);
		$retorno         = [
			'dados' => [],
			'total' => $query['total']
		];

		foreach ($query['dados'] as $indice => $item) {
			$retorno['dados'][] = [
				$item['id'],
				$item['titulo'],
				$item['id_livro'],
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
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		$cadastro = $this->model->carregar_post($id)[0];
		$cadastro['editor_texto'] = $cadastro['post'];

		$this->view->assign('cadastro', $cadastro);
		$this->view->assign('series', $this->model->load_active_list('serie', 'id, serie'));
		$this->view->assign('livros', $this->model->load_active_list('livro', 'id, titulo, titulo_original'));
	}


	public function middle_visualizar($id){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		$cadastro = $this->model->carregar_post($id)[0];
		$cadastro['editor_texto'] = $cadastro['post'];

		$this->view->assign('cadastro', $cadastro);
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
		$this->view->assign('anterior_post_posterior', $this->carregar_post_proximo_anterior($post[0]));
		$this->view->assign('livros', $this->universe->get_model('livro')->carregar_livro());
		$this->view->render_plataforma('', '', 'post', ['site_cabecalho', 'site_rodape', 'sidebar', 'menu', 'seo']);
	}

	private function carregar_post_proximo_anterior($post){
		$posts     = $this->universe->get_model('post')->carregar_post(null, ['post.id_livro' => $post['id_livro']]);
		$ordenados = [];

		foreach ($posts as $indice => $item) {
			$ordenados[$item['id']] = $item;
		}

		$indices   = array_flip(array_keys($ordenados));
		$ordenados = array_values($ordenados);

		$anterior_proximo = [
			'anterior'  => @$ordenados[$indices[$post['id']] - 1],
			'posterior' => @$ordenados[$indices[$post['id']] + 1],
		];

		return $anterior_proximo;
	}

	protected function before_insert($dados){
		$dados['post'] = $dados['editor_texto'];
		unset($dados['editor_texto']);
		return $dados;
	}

	protected function before_update($dados, $where){
		$dados['post'] = $dados['editor_texto'];
		unset($dados['editor_texto']);
		return $dados;
	}

	protected function cadastrar_seo($retorno){
		$retorno = parent::cadastrar_seo($retorno);

		unset($retorno['dados']['post']);

		$livro = $this->universe->get_model('livro')->carregar_livro($retorno['dados']['id_livro'])[0];

		$update = false;

		if(empty($retorno['seo']['dados']['keywords'])){
			$update = true;
			$retorno['seo']['dados']['keywords'] = "Mundo das Trevas: {$livro['titulo']}, World of Darkness: {$livro['titulo_original']}, World of Darkness, Mundo das Trevas, Portugues, Traduzido, Tradução, RPG";
		}

		if(empty($retorno['seo']['dados']['description'])){
			$update = true;
			$retorno['seo']['dados']['description'] = "{$retorno['dados']['titulo']} do Livro {$livro['titulo']} (book {$livro['titulo_original']})";
		}

		if(!empty($update)){
			$retorno['seo'] = $this->model
				->insert_update(
					'seo',
					['id_controller' => $retorno['id'], 'controller' => $this->modulo['modulo']],
					$retorno['seo']['dados'],
					true
				);
		}
	}
}