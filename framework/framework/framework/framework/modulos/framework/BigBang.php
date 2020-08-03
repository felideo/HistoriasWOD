<?php
namespace Framework;

class BigBang{

	private $url;
	private $universe;
	private $first_atoms;

	public function inflate(){
		$this->universe = Universe::get_universe();
		$this->url      = $this->universe->get_url();

		$this->is_nonexistent_file();

		if(empty($this->url['path'][0])){
			$this->is_index();
		}

		if(defined('DB_NAME') && defined('DB_HOST') && defined('DB_USER')  && defined('DB_PASS')){
			$this->load_friendly_url();
		}

		if(!file_exists(strtolower("{$this->url['core_module']}/{$this->url['path'][0]}/controller/{$this->url['path'][0]}.php"))){
			$this->full_entropy();
		}

		$this->form_first_atoms();

		$this->expand();
	}

	private function is_nonexistent_file(){
		if(substr_count(end($this->url['path']), '.') > 0){
			http_response_code (404);

			if(defined('DEVELOPER') && !empty(DEVELOPER)){
				debug2($this->url);
			}

			exit;
		}
	}

	private function is_index(){
		$this->first_atoms = [
			'file'       => 'modulos/index/controller/index.php',
			'class'      => 'Index',
			'method'     => 'index',
			'parameters' => null
		];

		$this->url['core_module'] = 'modulos';
		$this->universe->set_core_module('modulos');
		$this->expand();
	}

	private function load_friendly_url(){
	    $pdo    = new \PDO('mysql:dbname=' . DB_NAME . ";host=" . DB_HOST, DB_USER, DB_PASS);
	    $select = "SELECT controller, metodo, id_controller FROM `url` WHERE url = LOWER('{$this->url['path'][0]}') AND ativo = 1 LIMIT 1";

	    if(isset($this->url['path'][1])){
	    	$select = "SELECT controller, metodo, id_controller FROM `url` WHERE controller = LOWER('{$this->url['path'][0]}') AND url = LOWER('{$this->url['path'][1]}') AND ativo = 1 LIMIT 1";
	    }

	    $sql = $pdo->prepare($select);
		$sql->execute();
		$retorno = $sql->fetchAll(\PDO::FETCH_ASSOC);

		if(empty($retorno)){
			return;
		}

		$this->url['path'] = [
			$retorno[0]['controller'],
			$retorno[0]['metodo'],
			!empty($retorno[0]['id_controller']) ? $retorno[0]['id_controller'] : '',
		];

		$this->url['core_module'] = $this->universe->is_core_module($retorno[0]['controller'], $retorno[0]['controller']);
		$this->universe->set_core_module($this->url['core_module']);
	}

	private function form_first_atoms(){
		$this->first_atoms['file']       = strtolower("{$this->url['core_module']}/{$this->url['path'][0]}/controller/{$this->url['path'][0]}.php");
		$this->first_atoms['class']      = $this->url['path'][0];
		$this->first_atoms['method']     = isset($this->url['path'][1]) ? $this->url['path'][1] : 'index';
		$this->first_atoms['parameters'] = [];

		unset($this->url['path'][0], $this->url['path'][1]);

		if(!empty($this->url['path'])){
			$this->first_atoms['parameters'] = array_values($this->url['path']);
		}

		if(isset($this->url['query'])){
			$this->first_atoms['query'] = $this->url['query'];
		}
	}

	private function expand(){
		try{
			$controller = $this->universe
				->dark_ages()
				->first_star_light()
				->get_controller($this->first_atoms['class']);

			$controller->form_galaxies();

			$method = strtolower($this->first_atoms['method']);

			if(method_exists($controller, $method)){
				$controller->{$method}($this->first_atoms['parameters']);
				exit;
			}

			if(method_exists($controller, 'index')){
				$controller->index(array_merge([$this->first_atoms['method']], $this->first_atoms['parameters']));
				exit;
			}
		}catch(\Fail $e) {
			$e->show_error(true);
		}

		$this->full_entropy();
	}

	private function full_entropy(){
		if(empty(DEVELOPER)){
			header('Location: /error');
			exit;
		}

		// Metodo anterior. Tava dnado problema se tivece um modulo customizado
		$this->first_atoms = [
			'class'      => 'error',
			'method'     => 'index',
			'parameters' => $this->url
		];

		$this->url['core_module'] = 'framework/modulos';
		$this->universe->set_core_module('framework/modulos');
		$this->expand();
	}
}