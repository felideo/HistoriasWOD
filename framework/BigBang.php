<?php
namespace Framework;

class BigBang{
	private $url;
	private $file_class_method_parameters;

	public function __construct(){
		$this->get_url();
		$this->acelerate_return_inexistente_file();

		// if(empty($this->url['path'][0])){
		if($this->url['path'][0] === ''){
			$this->index();
		}

		$this->load_friendly_url();
		$this->identificar_arquivo_metodo_parametro();
		$this->execute();
	}

	private function get_url(){
		$this->url         = URL::get_url();
		$this->url         = ['full' => $this->url] + parse_url($this->url);
		$this->url['path'] = explode('/', trim($this->url['path'], '/'));
	}

	private function acelerate_return_inexistente_file(){
		if(count(explode('.', end($this->url['path']))) > 1){
			http_response_code (404);
			exit;
		}
	}

	private function index(){
		$this->file_class_method_parameters = [
			'file'       => 'modulos/index/controller/index.php',
			'class'      => 'Index',
			'method'     => 'index',
			'parameters' => null
		];

		$this->execute();
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

	private function identificar_arquivo_metodo_parametro(){
		$file_class_method_parameters = $this->url['path'];

		if(!file_exists("modulos/{$file_class_method_parameters[0]}/controller/{$file_class_method_parameters[0]}.php")){
			$this->error();
		}

		$this->file_class_method_parameters['file']  = "modulos/{$file_class_method_parameters[0]}/controller/{$file_class_method_parameters[0]}.php";
		$this->file_class_method_parameters['class'] = implode('_', array_map('ucfirst', explode('_', $file_class_method_parameters[0])));
		$this->file_class_method_parameters['method'] = isset($file_class_method_parameters[1]) ? $file_class_method_parameters[1] : 'index';
		unset($file_class_method_parameters[0], $file_class_method_parameters[1]);
		$this->file_class_method_parameters['parameters'] = [];

		if(!empty($file_class_method_parameters)){
			$this->file_class_method_parameters['parameters'] = array_values($file_class_method_parameters);
		}
	}

	private function execute(){
		try{
			require_once $this->file_class_method_parameters['file'];

			$controller = '\\Controller\\' . $this->file_class_method_parameters['class'];
			$controller = new $controller;

			if(method_exists($controller, $this->file_class_method_parameters['method'])){
				$controller->{$this->file_class_method_parameters['method']}($this->file_class_method_parameters['parameters']);
				exit;
			}

			if($this->file_class_method_parameters['method'] == 'index'){
				$controller->index($this->file_class_method_parameters['parameters']);
				exit;
			}
		}catch(\Erro $e) {
			$e->show_error(true);
		}

		$this->error();
	}

	private function error() {
		header('location: /error');
		exit;
	}
}