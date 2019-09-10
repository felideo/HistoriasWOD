<?php
namespace Controller;

class Podcast extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' => 'podcast',
		'name'   => 'Podcast',
		'send'   => 'Podcast',
		'table'  => 'podcast',
		'url'    => [
			'url'    => 'localizador',
			'metodo' => 'visualizar_front'
		]
	];

	protected $datatable = [
		'colunas'                => ['ID', 'Nome' ,'Descrição', 'Ações'],
		'select'                 => ['id', 'nome', 'descricao'],
		'from'                   => 'podcast',
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

	public function middle_editar($id){
		$cadastro = $this->model->carregar_podcast($id);
		$this->view->assign('cadastro', $cadastro);
	}

	public function middle_visualizar($id){
		$cadastro = $this->model->carregar_podcast($id);
		$this->view->assign('cadastro', $cadastro);
	}

	public function insert_update($dados, $where = null){

		$dados['podcast']['id_idioma']   = $this->tratar_idioma($dados['podcast']['id_idioma']);
		$dados['podcast']['data_inicio'] = transformar_data($dados['podcast']['data_inicio']);
		$dados['podcast']['localizador'] = \Libs\Strings::limpezaCompleta($dados['podcast']['nome']);
		$dados['podcast']['ativo']       = 1;

		if(!isset($where) || empty($where)){
			$where = ['localizador' => $dados['podcast']['localizador']];
		}

		$retorno = $this->model->insert_update(
			'podcast',
			$where,
			$dados['podcast'],
			true
		);

		if(empty($retorno['status'])){
			return $retorno;
		}

		$this->historico_edicao($retorno['id'], $retorno['operacao']);

		$retorno_url = (new \Libs\URL)->setId($retorno['id'])
			->setUrl($dados['podcast']['localizador'])
			->setController($this->modulo['modulo'])
			->setMetodo('visualizar_front')
			->cadastrarUrlAmigavel();

		return $retorno;
	}

	public function historico_edicao($id_podcast, $operacao){
		$insert_db = [
			'id_podcast' => $id_podcast,
			'id_usuario' => $_SESSION['usuario']['id'],
			'operacao'   => $operacao
		];

		$this->model->insert('podcast_historico_edicao', $insert_db);
	}

	private function tratar_idioma($id_idioma){
		if(is_numeric($id_idioma)){
			return $id_idioma;
		}

		$retorno = $this->universe->get_controller('idioma')->insert_update(['idioma' => $id_idioma], []);

		if(!empty($retorno['status'])){
			return $retorno['id'];
		}

		$this->view->warn_js('Ocorreu um erro ao cadastrar o idioma. Por favor tente novamente.', 'erro');
	}

	public function visualizar_front($id){
		$cadastro = $this->model->carregar_podcast($id[0]);
		$this->view->assign('cadastro', $cadastro);
		$this->view->render_plataforma('', '', 'podcast');
	}
}