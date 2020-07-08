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
			$this->universe->session->set('configuracoes', $this->model->full_load_by_id('configuracao', 1)[0]);
		}

		$this->url = $this->universe->get_url();

		$this->view->modulo = $this->modulo;
		$this->view->assign('modulo', $this->modulo);
		$this->view->assign('url', $this->url);
	}

	public function define_modulo(){
		if(!isset($this->modulo['modulo']) || empty($this->modulo['modulo'])){
			$this->modulo = [
				'modulo' 	=> 'generic'
			];
		}

		if(!isset($this->modulo['name']) || empty($this->modulo['name']) || !isset($this->modulo['send']) || empty($this->modulo['send'])){
			$pretty_name = explode('_', $this->modulo['modulo']);

			foreach($pretty_name as $indice => $item){
				if(strlen($item) > 2){
					$pretty_name[$indice] = ucfirst($item);
				}
			}

			$pretty_name = implode(' ', $pretty_name);
		}

		if(!isset($this->modulo['name']) || empty($this->modulo['name'])){
			$this->modulo['name'] = $pretty_name . 's';
		}

		if(!isset($this->modulo['send']) || empty($this->modulo['send'])){
			$this->modulo['send'] = $pretty_name;
		}

		if(!isset($this->modulo['table']) || empty($this->modulo['table'])){
			$this->modulo['table'] = $this->modulo['modulo'];
		}
	}

	public function check_if_exists($id, $table = null){
		if(!isset($table) || empty($table)){
			$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		}

		if(empty($id)){
			throw new \Fail("ID do cadastro não indicado na verificação de existencia do cadastro - {$table}");
		}

		if(empty($this->model->select("SELECT id FROM {$table} WHERE id = {$id} AND ativo = 1"))){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' não existe...', 'erro');
			header('location: /' . $this->modulo['modulo']);
			exit;
		}
	}

	public function carregar_front(){
		$front_controller = $this->universe->get_controller('front');
		$front_controller->carregar_cabecalho_rodape();

		return $front_controller;
	}
}