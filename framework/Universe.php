<?php
namespace Framework;

class Universe {
	private $url;
	public  $auth;
	private $view;
	public 	$session;
	public  $permission;
	private $controllers = [];
	private $core_module;

	public function get_url(){
		if(isset($this->url) && !empty($this->url)){
			return $this->url;
		}

		$url = new URL();

		$this->url = array_merge(['url' => $url->get_url()], $url->get_parsed());
		$this->url['path'] = explode('/', trim($this->url['path'], '/'));

		if(isset($this->url['query'])){
			parse_str($this->url['query'], $this->url['query']);
		}

		$this->url['core_module'] = $this->is_core_module($this->url['path'][0], $this->url['path'][0]);
		$this->core_module        = $this->url['core_module'];

		return $this->url;
	}

	public function dark_ages(){
		$this->auth       = new \Libs\Auth();
		$this->permission = new \Libs\Permission();
		$this->session    = new \Libs\Session();


		return $this;
	}

	public function first_star_light(){
		if(isset($this->view) && !empty($this->view)){
			return $this;
		}

		$this->view = new View();
		$this->view->set_universe($this)
			->shine();

		return $this;
	}

	public function get_controller($controller, $subcontroller = null){
		$controller    = strtolower($controller);
		$subcontroller = strtolower($subcontroller);
		$subcontroller = (!empty($subcontroller) ? $subcontroller : $controller);

		if(isset($this->controllers[$controller . '_' . $subcontroller]) && !empty($this->controllers[$controller . '_' . $subcontroller]) && is_object($this->controllers[$controller . '_' . $subcontroller])){
			return $this->controllers[$controller . '_' . $subcontroller];
		}

		$file = $this->is_core_module($controller, $subcontroller) . "/{$controller}/controller/{$subcontroller}.php";

		if(!file_exists($file)){
			throw new \Fail('Controller Inexistente ' . $controller . ' - ' . $subcontroller);
		}

		$instancia_controller = '\\Controller\\' . ucfirst($subcontroller);

		require_once $file;

		$instancia_controller = new $instancia_controller;

		$instancia_controller->set_universe($this)
			->set_model($this->get_model($controller, $subcontroller))
			->set_view($this->view);

		$this->controllers[$controller . '_' . $subcontroller] = $instancia_controller;

		return $this->controllers[$controller . '_' . $subcontroller];
	}

	public function get_model($model, $submodel = null){
		$model    = strtolower($model);
		$submodel = strtolower($submodel);
		$submodel = (!empty($submodel) ? $submodel : $model);

		if(isset($this->models[$model . '_' . $submodel]) && !empty($this->models[$model . '_' . $submodel]) && is_object($this->models[$model . '_' . $submodel])){
			return $this->models[$model . '_' . $submodel];
		}

		$file = $this->is_core_module($model, $submodel) . "/{$model}/model/{$submodel}.php";

		if(!file_exists($file)) {
			throw new \Fail('Model Inexistente ' . $model . ' - ' . $submodel);
		}

		$instancia_model = '\\Model\\' . ucfirst($submodel);

		require_once $file;

		$instancia_model = new $instancia_model;

		$instancia_model->set_universe($this);

		$this->models[$model . '_' . $submodel] = $instancia_model;

		return $this->models[$model . '_' . $submodel];
	}

	public function is_core_module($modulo, $submodulo){
		if(file_exists("modulos/{$modulo}/controller/{$submodulo}.php")){
			return 'modulos';
		}elseif(file_exists("framework/modulos/{$modulo}/controller/{$submodulo}.php")){
			return 'framework/modulos';
		}

		return null;
	}







	public function set_core_module($core_module){
		$this->core_module        = $core_module;
		$this->url['core_module'] = $core_module;
		return $this;
	}

	public function get_core_module(){
		return $this->core_module;
	}
}