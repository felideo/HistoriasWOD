<?php
namespace Controller;

class Plataforma extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' => 'plataforma',
		'name'   => 'HTML Cloud Editor',
	];

	protected $datatable = [
		'colunas'                => ['Identificador <i class="fa fa-search"></i>',  'Nome <i class="fa fa-search"></i>',  'Descricao <i class="fa fa-search"></i>',  'Ultima Atualizacao',  'Ultima Publicao', 'Ações'],
		'select'                 => ['id', 'identificador', 'nome', 'descricao', 'identificador', 'ultima_atualizacao', 'ultima_publicacao', ],
		'from'                   => 'plataforma',
		'search'                 => ['identificador', 'nome', 'descricao'],
		'ordenacao_desabilitada' => '5'
	];

	public function middle_index(){
		if(isset($_SESSION['plataforma']['modo_desenvolvedor']) && !empty($_SESSION['plataforma']['modo_desenvolvedor'])){
			$this->modulo['texto_adicional_cabecalho'] = ' - Modo Desenvolvedor Ativo';
			$this->view->assign('modulo', $this->modulo);
		}

		$this->view->assign('permissao_editar', $this->universe->permission->check_user_permission($this->modulo['modulo'], 'editar'));
	}

	protected function carregar_dados_listagem_ajax($busca){
		$query   = $this->model->carregar_listagem($busca, $this->datatable);
		$retorno = [];

        $botao = new \Libs\GerarBotoes();

		foreach ($query['dados'] as $indice => $item) {
			$botao->setTitle('Visualizar')
                ->setPermissao($this->universe->permission->check_user_permission($this->modulo['modulo'], 'visualizar'))
                ->setHref("/{$this->modulo['modulo']}/editor/{$item['id']}/visualizar")
                ->setTexto("<i class='botao_listagem fa fa-eye fa-fw'></i>")
                ->gerarBotao();

			$botao->setTitle('Editar')
                ->setPermissao($this->universe->permission->check_user_permission($this->modulo['modulo'], 'editar'))
                ->setHref("/{$this->modulo['modulo']}/editor/{$item['id']}")
                ->setTexto("<i class='botao_listagem fa fa-pencil fa-fw'></i>")
                ->gerarBotao();

			$botao->setTitle('Historico de Edições')
                ->setPermissao($this->universe->permission->check_user_permission($this->modulo['modulo'], 'editar'))
                ->setHref("/{$this->modulo['modulo']}/historico/{$item['id']}")
                ->setTexto("<i class='botao_listagem fa fa-history fa-fw'></i>")
                ->gerarBotao();

            $botao->setTitle('Publicar Última Versão')
                ->setPermissao($this->universe->permission->check_user_permission($this->modulo['modulo'], 'editar') && !in_array($item['id'] , [1, 2]))
                ->setHref("/{$this->modulo['modulo']}/publicar/{$item['id']}/{$item['identificador']}")
                ->setTexto("<i class='botao_listagem fa fa-cloud-upload fa-fw'></i>")
                ->gerarBotao();

			$retorno['dados'][] = [
				$item['identificador'],
				$item['nome'],
				$item['descricao'],
				$item['ultima_atualizacao'],
				$item['ultima_publicacao'],
				$botao->getBotoes()
			];
		}

		$retorno['total'] = $query['total'];
		return $retorno;
	}

	public function historico($parametros){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->view->assign('permissao_criar', $this->universe->permission->check_user_permission($this->modulo['modulo'], 'criar'));

		$this->datatable = [
			'colunas' => ['ID <i class="fa fa-search"></i>',  'Responsavel <i class="fa fa-search"></i>',  'Data', 'Publicado', 'Ações'],
			'search'  => ['id', 'nome', 'descricao'],
			'ordenacao_desabilitada' => '4'
		];

		if(isset($this->datatable) && !empty($this->datatable)){
			$this->view->assign('datatable', $this->datatable);
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$cadastro = $this->model->full_load_by_id('plataforma', $parametros[0])[0];

		$this->modulo['texto_adicional_cabecalho'] = ' - Historico de edição - ' . $cadastro['nome'];
		$this->view->assign('modulo', $this->modulo);

		$this->view->assign('cadastro', $cadastro);

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/historico');
	}

	public function carregar_dados_listagem_historico_ajax($parametros){
		$busca = [
			'order'  => carregar_variavel('order'),
			'search' => carregar_variavel('search'),
			'start'  => carregar_variavel('start'),
			'length' => carregar_variavel('length'),
		];

		$query   = $this->model->carregar_listagem_historico($parametros[0], $busca, $this->datatable);
		$retorno = [];

        $botao = new \Libs\GerarBotoes();

		foreach ($query as $indice => $item) {
			$botao->setTitle('Visualizar')
                ->setPermissao($this->universe->permission->check_user_permission($this->modulo['modulo'], 'visualizar'))
                ->setHref("/{$this->modulo['modulo']}/editor_historico/{$item['id']}/visualizar")
                ->setTexto("<i class='botao_listagem fa fa-eye fa-fw'></i>")
                ->gerarBotao();

			// $botao->setTitle('Editar')
   //              ->setPermissao($this->universe->permission->check_user_permission($this->modulo['modulo'], 'editar'))
   //              ->setHref("/{$this->modulo['modulo']}/editor/{$item['id']}")
   //              ->setTexto("<i class='botao_listagem fa fa-pencil fa-fw'></i>")
   //              ->gerarBotao();

            // $botao->setTitle('Publicar Última Versão')
            //     ->setPermissao($this->universe->permission->check_user_permission($this->modulo['modulo'], 'editar') && !in_array($item['id'] , [1, 2]))
            //     ->setHref("/{$this->modulo['modulo']}/publicar/{$item['id']}/{$item['identificador']}")
            //     ->setTexto("<i class='botao_listagem fa fa-cloud-upload fa-fw'></i>")
            //     ->gerarBotao();

                // debug2($item);

            switch ($item['publicado']) {
            	case '0':
            		$publicado = 'Nunca';
            		break;

            	case '1':
            		$publicado = 'Publicado Atualmente';
            		break;

            	case '2':
            		$publicado = 'Publicado no Passado';
            		break;
            }

			$retorno[] = [
				$item['id'],
				$item['pessoa'][0]['nome'] . ' ' . $item['pessoa'][0]['sobrenome'],
				$item['ultima_atualizacao'],
				$publicado,
				$botao->getBotoes()
			];
		}


		echo json_encode([
            "draw"            => intval(carregar_variavel('draw')),
            "recordsTotal"    => intval(count($retorno)),
            "recordsFiltered" => intval($this->model->select("SELECT count(id) AS total FROM plataforma_pagina WHERE ativo = 1 AND id_plataforma = {$parametros[0]}")[0]['total']),
            "data"            => $retorno
        ]);

        exit;
	}

	public function editor($parametros){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "criar");

		$permissao = 'editar';

		if(isset($parametros[1]) && $parametros[1] == 'visualizar'){
			$permissao = 'visualizar';
		}

		$this->universe->permission->check($this->modulo['modulo'], $permissao);
		// $this->check_if_exists($parametros[0], 'plataforma_pagina');

		if($permissao == 'visualizar'){
			$this->view->assign('read_only', true);
		}

		$cadastro = $this->model->full_load_by_id('plataforma', $parametros[0])[0];

		$this->modulo['texto_adicional_cabecalho'] = ' - ' . $cadastro['nome'];

		$this->view->assign('modulo', $this->modulo);
		$this->view->assign('titulo_pagina', 'HTML ' . $cadastro['nome']);

		$this->view->assign('cadastro', $cadastro);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/editor/editor');
	}

	public function editor_historico($parametros){
		$this->universe->auth->is_logged(true);

		$permissao = 'editar';

		if(isset($parametros[1]) && $parametros[1] == 'visualizar'){
			$permissao = 'visualizar';
		}

		$this->universe->permission->check($this->modulo['modulo'], $permissao);
		// $this->check_if_exists($parametros[0], 'plataforma_pagina');

		if($permissao == 'visualizar'){
			$this->view->assign('read_only', true);
		}

		$pagina = $this->model->full_load_by_id('plataforma_pagina', $parametros[0])[0];
		$cadastro = $this->model->full_load_by_id('plataforma', $pagina['id_plataforma'])[0];
		$cadastro['id_plataforma_pagina'] = $pagina['id'];

		$this->view->assign('cadastro', $cadastro);

		$this->modulo['texto_adicional_cabecalho'] = ' - ' . $cadastro['nome'] . ' - Versão de ' .  $pagina['ultima_atualizacao'];
		$this->view->assign('modulo', $this->modulo);

		$this->view->assign('reativar', true);

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/editor/editor');
	}

	public function load_source_code_ajax($parametros){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "editar");
		// $this->check_if_exists($parametros[0], 'plataforma_pagina');

		$parametros = carregar_variavel('data');

		echo json_encode($this->model->carregar_plataforma_pagina($parametros)[0]);
		exit;
	}

	public function save_source_code_ajax($parametros){
		$codigo_fonte['codigo_fonte'] = $_POST['data'];

		$insert_db = [
			'id_plataforma'      => $parametros[0],
			'id_usuario'         => $_SESSION['usuario']['id'],
			'html'               => $codigo_fonte['codigo_fonte'],
			'publicado'          => 0,
			'ativo'              => 1,
		];

		$codigo_fonte['retorno'] = $this->model->insert('plataforma_pagina', $insert_db);

		$retorno = false;

		if(!empty($codigo_fonte['retorno']['status'])){
			$retorno = true;

			$this->model->update(
				'plataforma',
				['ultima_atualizacao' => date("Y-m-d H:i:s")],
				['id' => $parametros[0]]
			);
		}

		echo json_encode($retorno);
		exit;
	}

	public function publicar_todas_paginas(){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], 'editar');

		$paginas = $this->model->load_active_list('plataforma', 'id, nome, identificador');

		foreach($paginas as $indice => $pagina){
			$retorno = $this->publicar_pagina($pagina['id'], $pagina['identificador']);

			if(!empty($retorno['status'])){
				$this->view->warn_js("Página: {$pagina['nome']} publicada com sucesso!", 'sucesso');
			}else{
				$this->view->warn_js("Ocorreu um erro ao publicar a página: {$pagina['nome']}", 'erro');
			}
		}

		header('location: /' . $this->modulo['modulo']);
		exit;
	}

	public function publicar($parametros){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], 'editar');

		$this->publicar_pagina(1, 'header');
		$this->publicar_pagina(2, 'footer');
		$retorno = $this->publicar_pagina($parametros[0], $parametros[1]);

		if(!empty($retorno['status'])){
			$this->view->warn_js("Página: publicada com sucesso!", 'sucesso');
		}else{
			$this->view->warn_js("Ocorreu um erro ao publicar a página!", 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;
	}

	public function publicar_pagina($id_plataforma, $identificador){
		$this->model->execute("UPDATE plataforma_pagina SET `publicado` = 2 WHERE `id_plataforma` = {$id_plataforma} AND`publicado` = 1;");

		usleep(100);

		$publicar = $this->model->query->select('pagina.id')
			->from('plataforma_pagina pagina')
			->where("pagina.id_plataforma = {$id_plataforma} AND pagina.ativo = 1")
			->orderBy('pagina.ultima_atualizacao DESC')
			->limit(1)
			->fetchArray()[0];

		usleep(200);

		$retorno = $this->model->update(
			'plataforma_pagina',
			['publicado' => 1],
			['id' => $publicar['id']]
		);

		usleep(200);

		if(empty($retorno['status'])){
			return $retorno;
		}

		$this->model->update('plataforma', ['ultima_publicacao' => date("Y-m-d H:i:s")], ['id' => $id_plataforma]);

		$retorno = $this->model->update(
			'plataforma',
			['ultima_publicacao' => date("Y-m-d H:i:s")],
			['id' => $id_plataforma]
		);

		if(file_exists('views/plataforma/' . $identificador . '.html')){
			unlink('views/plataforma/' . $identificador . '.html');
		}

		return $retorno;
	}

	public function ativar_modo_desenvolvedor($parametros){
		$parametros[0] = $parametros[0] == 1 ? true : false;

		$_SESSION['plataforma']['modo_desenvolvedor'] = $parametros[0];

		header('location: /' . $this->modulo['modulo']);
		exit;
	}
}