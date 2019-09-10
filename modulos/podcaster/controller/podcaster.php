<?php
namespace Controller;

class Podcaster extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' => 'podcaster',
		'name'   => 'Podcasters',
		'send'   => 'Podcaster',
		'table'  => 'podcaster'
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

	public function insert_update($dados, $where = null){
		debug2($dados);
		exit;

		if(isset($where['id']) && !empty($where['id'])){
			if(is_numeric($dados['nome'])){
				unset($dados['nome']);
			}

			if(isset($dados['id_usuario']) && !empty($dados['id_usuario'])){
				$where['id'] = $dados['id_usuario'];
				unset($dados['id_usuario']);
			}
		}

		$autor = [
			'pessoa'     => [
				'nome'      => str_replace(end(explode(' ', $dados['nome'])), '', $dados['nome']),
				'sobrenome' => end(explode(' ', $dados['nome'])),
				'link'      => $dados['link'],
				'autor'     => 1,
			],
			'usuario'    => [
				'email'      => $dados['email'],
				'hierarquia' => 10,
				'bloqueado'  => 1,
				'oculto'     => 1
			],
		];

		$controller_usuario = $this->get_controller('usuario');
		$orientador         = $controller_usuario->insert_update($autor, $where);

		return $orientador;
	}
}