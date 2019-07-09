<?php
namespace Framework;

use Libs\URL;

class ControllerCrud extends \Framework\Controller {
	protected $modulo    = [];
	protected $datatable = [];

	public function __construct() {
		parent::__construct();
		$this->view->modulo = $this->modulo;
		$this->view->assign('modulo', $this->modulo);
	}

	public function index() {
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$this->view->assign('permissao_criar', \Libs\Permission::check_user_permission($this->modulo['modulo'], 'criar'));

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

		echo json_encode([
            "draw"            => intval(carregar_variavel('draw')),
            "recordsTotal"    => intval(count($retorno)),
            "recordsFiltered" => intval($this->model->select("SELECT count(id) AS total FROM {$this->datatable['from']} WHERE ativo = 1")[0]['total']),
            "data"            => $retorno
        ]);

		exit;
	}

	public function create(){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "criar");

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

		header('location: /' . $this->modulo['modulo']);
		exit;
	}

	public function insert_update($dados, $where){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];

		return $this->model->insert_update(
			$table,
			$where,
			$dados,
			true
		);
	}

	private function cadastrar_url($dados){
		$url          = new URL;
		$retorno_url  = $url->setId($dados['id'])
			->setUrl($dados[$this->modulo['url']['url']])
			->setMetodo($this->modulo['url']['metodo'])
			->setController($this->modulo['modulo'])
			->cadastrarUrlAmigavel();
	}

	public function editar($id) {
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$this->middle_editar($id[0]);

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function update($id) {
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$dados   = carregar_variavel($this->modulo['modulo']);
		$retorno = $this->insert_update($dados, ['id' => $id[0]]);

		if(isset($this->modulo['url']) && !empty($this->modulo['url']) && !empty($retorno['status'])){
			$dados['id'] = $id[0];
			$this->cadastrar_url($dados);
		}

		if(isset($retorno['status']) && !empty($retorno['status'])){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' editado com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a edição do ' . strtolower($this->modulo['send']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;
	}

	public function visualizar($id){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);

		$this->middle_visualizar($id[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function destroy($id) {
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "deletar");

		$this->check_if_exists($id[0]);

		$retorno = $this->middle_delete($id[0]);

		if(isset($retorno['status']) && !empty($retorno['status'])){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' removido com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a remoção do ' . strtolower($this->modulo['send']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
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