<?php
namespace ControllerCore;

class Modulo extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'modulo',
	];

	protected $datatable = [
		'colunas'                => ['ID<i class="fa fa-search"></i>', 'Modulo<i class="fa fa-search"></i>', 'Ordem', 'Submenu', 'Acesso', 'Icone',  'Ações'],
		'from'                   => 'modulo',
		'ordenacao_desabilitada' => '3, 4, 5, 6'
	];

	public function middle_index() {
		$this->universe->auth->is_logged(true);
		$this->view->assign('submenu_list', $this->model->load_active_list('submenu'));
	}

	protected function carregar_dados_listagem_ajax($busca){
		$this->universe->auth->is_logged(true);
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query['dados'] as $indice => $item) {
			$retorno['dados'][] = [
				$item['id'],
				$item['nome'],
				$item['ordem'],
				isset($item['submenu'][0]['nome_exibicao']) ? $item['submenu'][0]['nome_exibicao'] : '',
				empty($item['hierarquia']) ? "Super Admin" : 'Hierarquico',
				"<i class='fa {$item['icone']} fa-fw'></i> {$item['icone']}",
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		$retorno['total'] = $query['total'];
		return $retorno;
	}

	public function editar($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], $this->modulo['modulo'] . "_" . "editar");

		$this->view->assign('cadastro', $this->model->full_load_by_id('modulo', $id[0]));
		$this->view->assign('submenu_list', $this->model->load_active_list('submenu'));
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function visualizar($id){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], $this->modulo['modulo'] . "_" . "visualizar");

		$this->view->assign('cadastro', $this->model->full_load_by_id('modulo', $id[0]));
		$this->view->assign('submenu_list', $this->model->load_active_list('submenu'));

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function create() {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], $this->modulo['modulo'] . "_" . "criar");
		$insert_db = carregar_variavel($this->modulo['modulo']);

		if(empty($insert_db['id_submenu'])){
			$insert_db['id_submenu'] = NULL;
		}

		$retorno = $this->model->insert($this->modulo['modulo'], $insert_db);

		if($retorno['status']){
			$retorno_permissoes = $this->model->permissoes_basicas($insert_db['modulo'], $retorno['id']);

			$path = "modulos/{$insert_db['modulo']}/";

			@mkdir($path . 'controller/', 0777, true );
			@mkdir($path . 'model/', 0777, true );
			@mkdir($path . 'view/form/', 0777, true );
			@mkdir($path . 'view/listagem/', 0777, true );

			file_put_contents("{$path}/controller/{$insert_db['modulo']}.php", '');
			file_put_contents("{$path}/model/{$insert_db['modulo']}.php", '');
			file_put_contents("{$path}/view/form/form.html", '');
			file_put_contents("{$path}/view/listagem/listagem.html", '');

			$this->universe->get_model('acesso')
				->set_usuario($_SESSION['usuario'])
				->carregar_dados_backend();
		}


		if($retorno['status'] && $retorno_permissoes[count($retorno_permissoes)]['erros'] == 0){
			$this->view->alert_js('Cadastro efetuado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo'] . '/listagem');
		exit;
	}

	public function update($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], $this->modulo['modulo'] . "_" . "editar");
		$update_db = carregar_variavel($this->modulo['modulo']);

		if(empty($update_db['id_submenu'])){
			$update_db['id_submenu'] = NULL;
		}

		$retorno = $this->model->update($this->modulo['modulo'], $update_db, ['id' => $id[0]]);

		if($retorno['status']){

			$this->universe->get_model('acesso')
				->set_usuario($_SESSION['usuario'])
				->carregar_dados_backend();

			$this->view->alert_js('Cadastro editado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a edição do cadastro, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo'] . '/listagem');
		exit;
	}

	public function delete($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], $this->modulo['modulo'] . "_" . "deletar");

		$retorno = $this->model->delete($this->modulo['modulo'], ['id' => $id[0]]);

		if($retorno['status']){

			$this->universe->get_model('acesso')
				->set_usuario($_SESSION['usuario'])
				->carregar_dados_backend();

			$this->view->alert_js('Remoção efetuada com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a remoção do cadastro, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo'] . '/listagem');
		exit;
	}
}



