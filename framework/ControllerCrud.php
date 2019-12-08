<?php
namespace Framework;

class ControllerCrud extends \Framework\Controller {
	protected $modulo    = [];
	protected $datatable = [];

	// public function __construct() {
	// 	parent::__construct();
	// 	$this->view->modulo = $this->modulo;
	// 	$this->view->assign('modulo', $this->modulo);
	// }

	public function listagem() {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->view->assign('permissao_criar', $this->universe->permission->check_user_permission($this->modulo['modulo'], 'criar'));

		if(isset($this->datatable) && !empty($this->datatable)){
			$this->view->assign('datatable', $this->datatable);
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$this->middle_index();

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	public function carregar_listagem_ajax(){
		$busca = [
			'order'  => carregar_variavel('order'),
			'search' => carregar_variavel('search'),
			'start'  => carregar_variavel('start'),
			'length' => carregar_variavel('length'),
		];

		$retorno = $this->carregar_dados_listagem_ajax($busca);

		if(!isset($retorno['dados'])){
			$retorno['dados'] = [];
		}

		echo json_encode([
            "draw"            => intval(carregar_variavel('draw')),
            "recordsTotal"    => intval(count($retorno['dados'])),
            "recordsFiltered" => intval($retorno['total']),
            "data"            => $retorno['dados']
        ]);

		exit;
	}

	public function create(){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "criar");

		$dados   = carregar_variavel($this->modulo['modulo']);
		$retorno = $this->insert_update($dados, []);

		if(isset($this->modulo['url']) && !empty($this->modulo['url']) && !empty($retorno['status'])){
			$dados['id'] = $retorno['id'];
			$this->cadastrar_url($dados);
		}

		if(isset($retorno['status']) && !empty($retorno['status'])){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' cadastrado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro do ' . strtolower($this->modulo['send']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo'] . '/listagem');
		exit;
	}

	public function insert_update($dados, $where){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];

		if(isset($this->modulo['localizador']) && !empty($this->modulo['localizador'])){
			$dados['localizador'] = \Libs\Strings::limpezaCompleta($dados[$this->modulo['localizador']]);

			if(!isset($where) || empty($where)){
				$where['localizador'] = \Libs\Strings::limpezaCompleta($dados[$this->modulo['localizador']]);
			}
		}

		return $this->model->insert_update(
			$table,
			$where,
			$dados,
			true
		);
	}

	protected function cadastrar_url($dados){
		$url          = new \Libs\URL;
		$retorno_url  = $url->setId($dados['id'])
			->setUrl($dados[$this->modulo['url']['url']])
			->setMetodo($this->modulo['url']['metodo'])
			->setController($this->modulo['modulo'])
			->atualizar(isset($this->modulo['url']['atualizar']) && !empty($this->modulo['url']['atualizar']))
			->cadastrarUrlAmigavel();
	}

	public function editar($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$this->middle_editar($id[0]);

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function update($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$dados   = carregar_variavel($this->modulo['modulo']);
		$retorno = $this->insert_update($dados, ['id' => $id[0]]);

		// if(isset($this->modulo['url']) && !empty($this->modulo['url']) && !empty($retorno['status'])){
		// 	$dados['id'] = $id[0];
		// 	$this->cadastrar_url($dados);
		// }

		if(isset($retorno['status']) && !empty($retorno['status'])){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' editado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a edição do ' . strtolower($this->modulo['send']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo'] . '/listagem');
		exit;
	}

	public function visualizar($id){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$this->middle_visualizar($id[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function destroy($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "deletar");

		$this->check_if_exists($id[0]);

		$retorno = $this->middle_delete($id[0]);

		if(isset($retorno['status']) && !empty($retorno['status'])){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' removido com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a remoção do ' . strtolower($this->modulo['send']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo'] . '/listagem');
		exit;
	}

	public function middle_index(){
	}

	public function middle_adicionar(){
	}

	public function middle_editar($id){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		$this->view->assign('cadastro', $this->model->full_load_by_id($table, $id)[0]);
	}

	public function middle_visualizar($id){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		$this->view->assign('cadastro', $this->model->full_load_by_id($table, $id)[0]);
	}

	public function middle_delete($id){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		return $this->model->delete($table, ['id' => $id]);
	}
}