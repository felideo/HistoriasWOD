<?php
namespace Framework;

class BigBang{
	private $url;
	private $file_class_method_parameters;

	public function __construct() {
		$this->get_url();
		$this->acelerate_return_inexistente_file();

		if(!empty($this->return_false)){
			http_response_code (404);
			return false;
		}

		if(empty($this->url[0])) {
			$this->file_class_method_parameters = [
				'file'       => 'modulos/index/controller/index.php',
				'class'      => 'Index',
				'method'     => 'index',
				'parameters' => null
			];

			$this->execute();
		}

		$this->load_friendly_url();

		$this->identificar_arquivo_metodo_parametro();

		$this->execute();
	}

	private function get_url(){
		$this->url = isset($_GET['url']) ? $_GET['url'] : null;
		$this->url = rtrim($this->url, '/');
		$this->url = filter_var($this->url, FILTER_SANITIZE_URL);
		$this->url = explode('/', $this->url);
	}

	private function acelerate_return_inexistente_file(){
		$last = end($this->url);
		if(count(explode('.', $last)) > 1){
			$this->return_false = true;
		}

	}

	private function execute(){
		$this->validate_execution();

		try{
			require_once $this->file_class_method_parameters['file'];

			$controller = '\\Controller\\' . $this->file_class_method_parameters['class'];
			$controller = new $controller;

			if(method_exists($controller, $this->file_class_method_parameters['method'])){
				$controller->{$this->file_class_method_parameters['method']}($this->file_class_method_parameters['parameters']);
			}

			if(method_exists($controller, 'index')){
				if(empty($this->file_class_method_parameters['method'])){
					$controller->index($this->file_class_method_parameters['parameters']);
					exit;
				}

				header('location: /' . strtolower($this->file_class_method_parameters['class']));
				exit;
			}

		}catch(\Error $e) {
			$e->show_error(true);
		}

		$this->error();
	}

	private function validate_execution(){
		if(!isset($this->file_class_method_parameters['file']) || empty($this->file_class_method_parameters['file'])){
			$this->error();
			// throw new \Error('Erro ao identificar o arquivo a ser carregado.');
		}

		if(!isset($this->file_class_method_parameters['class']) || empty($this->file_class_method_parameters['class'])){
			$this->error();
			// throw new \Error('Erro ao identificar a classe a ser instanciada.');
		}

		if(!isset($this->file_class_method_parameters['method']) || empty($this->file_class_method_parameters['method'])){
			// $this->error();
			// throw new \Error('Erro ao identificar o metodo a ser executado.');
		}
	}

	private function load_friendly_url(){
		// if(!isset($this->url[1])){
		// 	return false;
		// }

	    $pdo = new \PDO('mysql:dbname=' . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASS);

	    $select = "SELECT controller, metodo, id_controller FROM `url` WHERE url = '{$this->url[0]}' AND ativo = 1";

	    if(isset($this->url[1])){
	    	$select = "SELECT controller, metodo, id_controller FROM `url` WHERE controller = '{$this->url[0]}' AND url = '{$this->url[1]}' AND ativo = 1";

	    }

	    // $sql = $pdo->prepare("SELECT controller, metodo, id_controller FROM `url` WHERE controller = '{$this->url[0]}' AND url = '{$this->url[1]}' AND ativo = 1");
	    // $sql = $pdo->prepare("SELECT controller, metodo, id_controller FROM `url` WHERE url = '{$this->url[0]}' AND ativo = 1");
	    $sql = $pdo->prepare($select);


		$sql->execute();

		$retorno = $sql->fetchAll(\PDO::FETCH_NUM);

		if(!empty($retorno)){
			$this->url = [
				$retorno[0][0],
				$retorno[0][1],
				$this->url[0],
			];
		}
	}

	private function identificar_arquivo_metodo_parametro(){
		$file = 'modulos';

		foreach($this->url as $indice => $value) {
			if(file_exists("{$file}/{$value}/controller/{$value}.php") && empty($arquivo)){
				$arquivo = "{$file}/{$value}/controller/{$value}.php";
				$class = $value;
			}else{
				$method[] = $this->url[$indice];
			}
		}

		if(isset($method)){
			$metodo = $method[0];
			unset($method[0]);
		}

		$this->file_class_method_parameters =  [
			'file'       => $arquivo,
			'class'      => implode('_', array_map('ucfirst', explode('_', $class))),
			'method'     => isset($metodo) ? $metodo : null,
			'parameters' => isset($method) ? array_values($method) : null
		];
	}

	private function error() {

		header('location: /error');
		exit;
	}
}