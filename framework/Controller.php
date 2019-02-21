<?php
namespace Framework;

abstract class Controller {
	private $models      = [];
	private $controllers = [];

	public function __construct() {
		$this->define_modulo();

		$_SESSION['modulo_ativo'] = $this->modulo['modulo'];

		$model = explode('\\', get_class($this));
		$this->model = $this->get_model(strtolower(end($model)));

		// $_SESSION['configuracoes'] = $this->model->full_load_by_id('configuracao', 1)[0];
		$this->view = new View();

		$this->view->modulo = $this->modulo;

		$this->view->assign('url', @defined(URL) ? URL : '');
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

	public function set_view(&$view){
		$this->view = $view;
		return $this;
	}

	public function get_controller($controller, $subcontroller = null){
		if(isset($this->controllers[$controller . '_' . $subcontroller]) && !empty($this->controllers[$controller . '_' . $subcontroller]) && is_object($this->controllers[$controller . '_' . $subcontroller])){
			return $this->controllers[$controller . '_' . $subcontroller];
		}

		$subcontroller = (!empty($subcontroller) ? $subcontroller : $controller);
		$file          = "modulos/{$controller}/controller/{$subcontroller}.php";

		if(!file_exists($file)){
			throw new \Framework\Error('Controller Inexistente ' . $controller . ' - ' . $subcontroller);
		}

		$instancia_controller = '\\Controller\\' . ucfirst($subcontroller);
		require_once $file;
		$controller = new $instancia_controller;

		if(isset($this->view) && !empty($this->view)){
			return $controller->set_view($this->view);
		}

		$this->controllers[$controller . '_' . $subcontroller] = $controller;

		return $this->controllers[$controller . '_' . $subcontroller];
	}

	public function get_model($model, $submodel = null){
		if(isset($this->models[$model . '_' . $submodel]) && !empty($this->models[$model . '_' . $submodel]) && is_object($this->models[$model . '_' . $submodel])){
			return $this->models[$model . '_' . $submodel];
		}

		$submodel = (!empty($submodel) ? $submodel : $model);
		$file          = "modulos/{$model}/model/{$submodel}.php";

		if(!file_exists($file)) {
			// return new GenericModel();
			throw new \Framework\Error('Model Inexistente ' . $model . ' - ' . $submodel);
		}

		$instancia_model = '\\Model\\' . ucfirst($submodel);

		require_once $file;

		$this->models[$model . '_' . $submodel] = new $instancia_model;

		return $this->models[$model . '_' . $submodel];
	}

	public function check_if_exists($id, $table = null){
		if(!isset($table) || empty($table)){
			$table = isset($this->modulo['table']) ? $this->modulo['table'] : $this->modulo['modulo'];
		}

		if(empty($this->model->select("SELECT id FROM {$table} WHERE id = {$id} AND ativo = 1"))){
			$this->view->alert_js(ucfirst($this->modulo['send']) . ' não existe...', 'erro');
			header('location: /' . $this->modulo['modulo']);
			exit;
		}
	}

	public function set_sessao($parametros){
		$_SESSION[$parametros[0]] = $parametros[1];
		debug2('$_SESSION[' . $parametros[0] . '] = ' . $parametros[1]);
		exit;
	}

	public function print_sessao(){
		debug2($_SESSION);
		exit;
	}

	public function limpar_sessao(){
		session_unset();
		session_destroy();
		debug2('Sessão limpa! \o/');
		exit;
	}
}