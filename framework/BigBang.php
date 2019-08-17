<?php
namespace Framework;

class BigBang{
	private $url;
	private $file_class_method_parameters;

	public function expanse(){
		$this->get_url();
		$this->acelerate_return_inexistente_file();
		$this->is_index();
		$this->load_friendly_url();
		$this->is_core_module();
		$this->identificar_arquivo_metodo_parametro();
		$this->execute();
	}

	private function get_url(){
		$url = new URL();

		$this->url = array_merge(['url' => $url->get_url()], $url->get_parsed());
		$this->url['path'] = explode('/', trim($this->url['path'], '/'));

		if(isset($this->url['query'])){
			parse_str($this->url['query'], $this->url['query']);
		}
	}

	private function acelerate_return_inexistente_file(){
		if(substr_count(end($this->url['path']), '.') > 0){
			http_response_code (404);
			exit;
		}
	}

	private function is_index(){
		if(empty($this->url['path'][0])){
			$this->file_class_method_parameters = [
				'file'       => 'modulos/index/controller/index.php',
				'class'      => 'Index',
				'method'     => 'index',
				'parameters' => null
			];
			$this->url['core_module'] = 'modulos';
			$this->execute();
		}
	}

	private function load_friendly_url(){
	    $pdo    = new \PDO('mysql:dbname=' . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASS);
	    $select = "SELECT controller, metodo, id_controller FROM `url` WHERE url = '{$this->url['path'][0]}' AND ativo = 1 LIMIT 1";

	    if(isset($this->url['path'][1])){
	    	$select = "SELECT controller, metodo, id_controller FROM `url` WHERE controller = '{$this->url['path'][0]}' AND url = '{$this->url['path'][1]}' AND ativo = 1 LIMIT 1";

	    }

	    $sql = $pdo->prepare($select);
		$sql->execute();
		$retorno = $sql->fetchAll(\PDO::FETCH_ASSOC);

		if(!empty($retorno)){
			$this->url['path'] = [
				$retorno[0]['controller'],
				$retorno[0]['metodo'],
				!empty($retorno[0]['id_controller']) ? $retorno[0]['id_controller'] : '',
			];
		}
	}

	private function is_core_module(){
		$this->url['core_module'] = null;

		if(file_exists("modulos/{$this->url['path'][0]}/controller/{$this->url['path'][0]}.php")){
			$this->url['core_module'] = 'modulos';
			return;
		}elseif(file_exists("framework/modulos/{$this->url['path'][0]}/controller/{$this->url['path'][0]}.php")){
			$this->url['core_module'] = 'framework/modulos';
			return;
		}
	}

	private function identificar_arquivo_metodo_parametro(){
		if(!file_exists("{$this->url['core_module']}/{$this->url['path'][0]}/controller/{$this->url['path'][0]}.php")){
			$this->error();
		}

		$this->file_class_method_parameters['file']   = "{$this->url['core_module']}/{$this->url['path'][0]}/controller/{$this->url['path'][0]}.php";
		$this->file_class_method_parameters['class']  = $this->url['path'][0];
		$this->file_class_method_parameters['method'] = isset($this->url['path'][1]) ? $this->url['path'][1] : 'index';
		unset($this->url['path'][0], $this->url['path'][1]);
		$this->file_class_method_parameters['parameters'] = [];

		if(!empty($this->url['path'])){
			$this->file_class_method_parameters['parameters'] = array_values($this->url['path']);
		}

		if(isset($this->url['query'])){
			$this->file_class_method_parameters['parameters']['query'] = $this->url['query'];
		}
	}

	private function execute(){
		try{
			require_once $this->file_class_method_parameters['file'];

			$controller = '\\Controller\\' . $this->file_class_method_parameters['class'];
			$controller = new $controller();
			$controller->set_core_module($this->url['core_module'])
				->execute();

			if(method_exists($controller, $this->file_class_method_parameters['method'])){
				$controller->{$this->file_class_method_parameters['method']}($this->file_class_method_parameters['parameters']);
				exit;
			}

			$controller->index(array_merge([$this->file_class_method_parameters['method']], $this->file_class_method_parameters['parameters']));
			exit;
		}catch(\Fail $e) {
			$e->show_error(true);
		}

		$this->error();
	}

	private function error() {
		header('location: /error');
		exit;
	}
}