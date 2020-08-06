<?php
namespace Framework;

abstract class Controller {
	protected $url;
	protected $view;
	protected $model;
	protected $universe;

	public function set_universe($universe){
		$this->universe = $universe;
		return $this;
	}

	public function set_model($model){
		$this->model = $model;
		return $this;
	}

	public function set_view($view){
		$this->view = $view;
		return $this;
	}

	public function form_galaxies(){
		$this->define_modulo();

		$this->universe->session->set('modulo_ativo',  $this->modulo['modulo']);

		if(defined('DB_NAME') && defined('DB_HOST') && defined('DB_USER')  && defined('DB_PASS')){
			$configuracoes = $this->model->full_load_by_id('configuracao', 1, 'configuracao');

			$this->universe->session->set('configuracoes', $configuracoes);
			$this->view->assign('app_name', $configuracoes['aplicacao_nome']);
		}

		$this->url = $this->universe->get_url();

		$this->view->modulo = $this->modulo;
		$this->view->assign('modulo', $this->modulo);
		$this->view->assign('url', $this->url);
	}

	public function define_modulo(){
		if(empty($this->modulo['modulo'])){
			$this->modulo = [
				'modulo' 	=> 'generic'
			];
		}

		if(empty($this->modulo['name']) || empty($this->modulo['send'])){
			$pretty_name = explode('_', $this->modulo['modulo']);

			foreach($pretty_name as &$item){
				if(strlen($item) > 2){
					$item = ucfirst($item);
				}
			}

			$pretty_name = implode(' ', $pretty_name);
		}

		if(empty($this->modulo['name'])){
			$this->modulo['name'] = $pretty_name . 's';
		}

		if(empty($this->modulo['send'])){
			$this->modulo['send'] = $pretty_name;
		}

		if(empty($this->modulo['table'])){
			$this->modulo['table'] = $this->modulo['modulo'];
		}
	}

	public function carregar_front(){
		$front_controller = $this->universe->get_controller('front');
		$front_controller->carregar_cabecalho_rodape();

		return $front_controller;
	}

	protected function check_if_exists($id, $table = null){
		if(empty($table)){
			$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		}

		if(empty($id)){
			throw new \Fail("ID do cadastro não indicado na verificação de existencia do cadastro - {$table}");
		}

		if(empty($this->model->select("SELECT id FROM {$table} WHERE id = {$id} AND ativo = 1"))){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' não existe...', 'erro');
			$this->redirect("/{$this->modulo['modulo']}");
		}
	}

	protected function redirect($url){
		header('location: ' . $url);
		exit;
	}
}