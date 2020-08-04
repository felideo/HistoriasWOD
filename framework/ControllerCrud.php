<?php
namespace Framework;

class ControllerCrud extends \Framework\Controller {
	protected $modulo    = [];
	protected $datatable = [];

	public function listagem() {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->view->assign('permissao_criar', $this->universe->permission->check_user_permission($this->modulo['modulo'], 'criar'));

		if(!empty($this->datatable)){
			$this->view->assign('datatable', $this->datatable);
			$this->view->set_colunas_datatable($this->datatable['colunas']);
		}

		$this->middle_index();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/listagem/listagem');
	}

	public function carregar_listagem_ajax(){
		$this->universe->auth->is_logged(true);
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
		$dados   = $this->before_insert($dados);
		$retorno = $this->insert_update($dados, []);

		if(!empty($retorno)){
			$this->after_insert($retorno);
		}

		$msg = [
			'msg'    => ucfirst($this->modulo['send']) . ' cadastrado com sucesso!!!',
			'status' => 'sucesso'
		];

		if(empty($retorno['status'])){
			$msg = [
				'msg'    => 'Ocorreu um erro ao cadastrar ' . strtolower($this->modulo['send']) . ', por favor tente novamente...',
				'status' => 'erro'
			];

			if(DEVELOPER){
				$msg['msg'] .= "<br><br> {$retorno['erros_info']}";
			}
		}

		$this->view->alert_js($msg['msg'], $msg['status']);
		$this->redirect("/{$this->modulo['modulo']}/listagem");
	}

	public function update($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);

		$dados = carregar_variavel($this->modulo['modulo']);
		$dados = $this->before_update($dados, $where);
		$retorno = $this->insert_update($dados, ['id' => $id[0]]);

		if(!empty($retorno['status'])){
			$this->after_update($retorno);
		}

		$msg = [
			'msg'    => ucfirst($this->modulo['send']) . ' editado com sucesso!!!',
			'status' => 'sucesso'
		];

		if(empty($retorno['status'])){
			$msg = [
				'msg'    => 'Ocorreu um erro ao editar ' . strtolower($this->modulo['send']) . ', por favor tente novamente...',
				'status' => 'erro'
			];

			if(DEVELOPER){
				$msg['msg'] .= "<br><br> {$retorno['erros_info']}";
			}
		}

		$this->view->alert_js($msg['msg'], $msg['status']);
		$this->redirect("/{$this->modulo['modulo']}/listagem");
	}

	public function destroy($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "deletar");

		$this->check_if_exists($id[0]);

		$retorno = $this->middle_delete($id[0]);

		$msg = [
			'msg'    => ucfirst($this->modulo['send']) . ' removido com sucesso!!!',
			'status' => 'sucesso'
		];

		if(empty($retorno['status'])){
			$msg = [
				'msg'    => 'Ocorreu um erro ao remover ' . strtolower($this->modulo['send']) . ', por favor tente novamente...',
				'status' => 'erro'
			];

			if(DEVELOPER){
				$msg['msg'] .= "<br><br> {$retorno['erros_info']}";
			}
		}

		$this->view->alert_js($msg['msg'], $msg['status']);
		$this->redirect("/{$this->modulo['modulo']}/listagem");
	}

	protected function insert_update($dados, $where){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];

		if(!empty($this->modulo['localizador'])){
			$this->criar_localizador_unico();
		}

		$retorno = $this->model
			->insert_update(
				$table,
				$where,
				$dados,
				true
			);

		return $retorno;
	}

	protected function criar_localizador_unico(&$dados, &$where){
		$dados['localizador'] = \Libs\Strings::limpezaCompleta($dados[$this->modulo['localizador']]);

		if(empty($where)){
			$where['localizador'] = \Libs\Strings::limpezaCompleta($dados[$this->modulo['localizador']]);
		}
	}

	protected function cadastrar_url($dados){
		$this->universe->auth->is_logged(true);

		$url = new \Libs\URL;

		$retorno_url = $url->setId($dados['id'])
			->setUrl($dados[$this->modulo['url']['coluna']])
			->setMetodo($this->modulo['url']['metodo'])
			->setController($this->modulo['modulo'])
			->atualizar(!empty($this->modulo['url']['atualizar']))
			->cadastrarUrlAmigavel();
	}

	public function editar($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "editar");

		$this->check_if_exists($id[0]);
		$this->middle_editar($id[0]);

		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	public function visualizar($id){
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "visualizar");

		$this->check_if_exists($id[0]);
		$this->middle_visualizar($id[0]);

		$this->view->lazy_view();
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/form/form');
	}

	protected function middle_index(){
	}

	protected function middle_editar($id){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		$this->view->assign('cadastro', $this->model->full_load_by_id($table, $id, $this->modulo['modulo']));
	}

	protected function middle_visualizar($id){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		$this->view->assign('cadastro', $this->model->full_load_by_id($table, $id, $this->modulo['modulo']));
	}

	protected function middle_delete($id){
		$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		return $this->model->delete($table, ['id' => $id]);
	}

	protected function before_insert($dados){
		return $dados;
	}

	protected function before_update($dados, $where){
		return $dados;
	}

	protected function after_insert($retorno){
		if(!empty($this->modulo['url']['coluna']) && !empty($retorno['status'])){
			$this->cadastrar_url($retorno['dados']);
		}

		if(!empty($this->modulo['seo']['habilitado']) && !empty($retorno['status'])){
			$this->cadastrar_seo($retorno);
		}
	}

	protected function after_update($retorno){
		if(!empty($this->modulo['url']['coluna']) && !empty($retorno['status'])){
			$this->cadastrar_url($retorno['dados']);
		}

		if(!empty($this->modulo['seo']['habilitado']) && !empty($retorno['status'])){
			$this->cadastrar_seo($retorno);
		}
	}

	protected function cadastrar_seo($retorno){
		$seo = carregar_variavel('seo');

		if(empty(trim($seo['title'])) && !empty($this->modulo['seo']['coluna'])){
			$seo['title'] = $retorno['dados'][$this->modulo['seo']['coluna']];
		}

		if(empty(trim($seo['robots'])) && !empty($this->modulo['seo']['robots_padrao'])){
			$seo['robots'] = $this->modulo['seo']['robots_padrao'];
		}

		if(empty(trim($seo['revise'])) && !empty($this->modulo['seo']['revise_padrao'])){
			$seo['revise'] = $this->modulo['seo']['revise_padrao'];
		}

		$seo['id_controller'] = $retorno['id'];
		$seo['controller']    = $this->modulo['modulo'];
		$seo['ativo']         = 1;

		$retorno = $this->model
			->insert_update(
				'seo',
				['id_controller' => $retorno['id'], 'controller' => $this->modulo['modulo']],
				$seo,
				true
			);
	}
}