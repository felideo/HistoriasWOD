<?php
namespace Controller;

use Libs;

class Hierarquia extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'hierarquia',
	];

	protected $colunas = ['ID', 'Nome', 'Ações'];

	protected $datatable = [
		'colunas' => ['ID <i class="fa fa-search"></i>', 'Nome <i class="fa fa-search"></i>', 'Ações'],
		'from'    => 'hierarquia',
		'ordenacao_desabilitada' => '2'
	];

	public function listagem() {
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");
		$this->view->assign('permissao_criar', $this->universe->permission->check_user_permission($this->modulo['modulo'], 'criar'));


		$this->view->set_colunas_datatable(['ID', 'Nome', 'Nivel', 'Ações']);
		// $this->listagem($this->model->load_active_list($this->modulo['modulo']));

		$this->view->assign('permissoes_list', $this->universe->get_model('permissao')->load_permissions_list());
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');

	}

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca);

		$retorno = [];

		foreach ($query['dados'] as $indice => $item) {
			$retorno['dados'][] = [
				$item['id'],
				$item['nome'],
				$item['nivel'],
				$item['id'] != 1 ? $this->view->default_buttons_listagem($item['id'], true, true, true) : $this->view->default_buttons_listagem($item['id'], true, true, false)
			];
		}

		$retorno['total'] = $query['total'];
		return $retorno;
	}

	public function editar($id) {
		$this->universe->permission->check($this->modulo['modulo'], "editar");

		$this->view->assign('cadastro', $this->model->load_hierarquia($id[0]));
		$this->view->assign('permissoes_list', $this->universe->get_model('permissao')->load_permissions_list());
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function visualizar($id){
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->view->assign('cadastro', $this->model->load_hierarquia($id[0]));
		$this->view->assign('permissoes_list', $this->universe->get_model('permissao')->load_permissions_list());
		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function create() {
		$this->universe->permission->check($this->modulo['modulo'], "criar");

		$insert_db = carregar_variavel($this->modulo['modulo']);

		$retorno   = $this->model->insert($this->modulo['modulo'], $insert_db);

		if($retorno['status']){
			$hierarquia_relaciona_permissao = carregar_variavel('hierarquia_relaciona_permissao');

			if(isset($hierarquia_relaciona_permissao) && !empty(carregar_variavel('hierarquia_relaciona_permissao'))){
				foreach ($hierarquia_relaciona_permissao as $indice => $permissao) {
						$insert_permissao = [
							'id_hierarquia' => $retorno['id'],
							'id_permissao' => $permissao
						];

					$retorno_permissoes[] = $this->model->insert('hierarquia_relaciona_permissao', $insert_permissao);
					unset($insert_permissao);

				}
			}
		}

		if($retorno['status'] && $retorno_permissoes[count($retorno_permissoes)]['erros'] == 0){
			$this->view->alert_js('Cadastro efetuado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;
	}

	public function update($id) {
		$this->universe->permission->check($this->modulo['modulo'], "editar");

		$update_db = carregar_variavel($this->modulo['modulo']);
		$retorno = $this->model->update($this->modulo['modulo'], $update_db, ['id' => $id[0]]);

		if($retorno['status']){
			$retorno = $this->model->update_relacao('hierarquia_relaciona_permissao', 'id_hierarquia', $id[0], ['ativo' => 0]);
			foreach (carregar_variavel('hierarquia_relaciona_permissao') as $indice => $permissao) {
				$insert_permissao = [
					'id_hierarquia' => $id[0],
					'id_permissao' => $permissao
				];

				$retorno_permissoes[] = $this->model->insert('hierarquia_relaciona_permissao', $insert_permissao);
				unset($insert_permissao);
			}
		}

		if($retorno['status']){
			$this->view->alert_js('Cadastro editado com sucesso!!!', 'sucesso');

			$this->universe->session->remove('permissoes');

				$hierarquia = empty($_SESSION['usuario']['hierarquia']) ? 'NULL' : $_SESSION['usuario']['hierarquia'];

				$select = 'SELECT hierarquia.id as id_hierarquia, hierarquia.nome,'
					. ' relacao.id as id_relacao,'
					. ' permissao.id as id_permissao, permissao.permissao, permissao.id_modulo,'
					. ' modulo.modulo'
					. ' FROM hierarquia hierarquia'
					. ' LEFT JOIN hierarquia_relaciona_permissao relacao'
					. ' ON relacao.id_hierarquia = hierarquia.id AND relacao.ativo = 1'
					. ' LEFT JOIN permissao permissao'
					. ' ON permissao.id = relacao.id_permissao'
					. ' LEFT JOIN modulo modulo'
					. ' ON modulo.id = permissao.id_modulo'
					. ' WHERE hierarquia.id = ' . $hierarquia;

				$permissoes = $this->model->select($select);

				if(!empty($permissoes)){
					foreach($permissoes as $indice => $permissao){
						$retorno_permissoes[$permissao['modulo']][$permissao['permissao']] = $permissao;
					}

					$this->universe->session->set('permissoes', $retorno_permissoes);
				}

		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a edição do cadastro, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;
	}

	public function delete($id) {

		$this->universe->permission->check($this->modulo['modulo'], "deletar");

		$retorno = $this->model->delete($this->modulo['modulo'], ['id' => $id[0]]);
		// $retorno = $this->model->delete('permissao', $id[0]);
		$retorno = $this->model->delete('hierarquia_relaciona_permissao', ['id_hierarquia' => $id[0]]);


		if($retorno['status']){
			$this->view->alert_js('Remoção efetuada com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a remoção do cadastro, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;
	}
}