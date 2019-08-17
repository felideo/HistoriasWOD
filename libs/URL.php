<?php
namespace Libs;
use Libs\Strings;

class URL {
	private $id;
	private $url;
	private $model;
	private $controller;
	private $metodo;

	public function __construct(){
		$this->model = new \Framework\GenericModel();
	}

	public function cadastrarUrlAmigavel(){
		$this->url = $this->get_url_amigavel($this->url);
		$this->tratar_preexistencia();

		$retorno = $this->cadastrarUrl();

		if(!empty($retorno['status'])){
			return true;
		}

		return false;
	}

	private function get_url_amigavel($url){
    	$url = Strings::remover_caracteres_especiais($url, ['-']);
		$url = Strings::limparStringCompleto($url);
		$url = Strings::remover_acentos($url);
		$url = Strings::removerCaracteresMultiplicados('-', $url);

		return $url;
	}

	private function tratar_preexistencia(){
		$ja_existe     = true;
		$diferenciador = 1;
		$url_unica     = $this->url;

		while(!empty($ja_existe)) {
			$query = $this->model->select("SELECT id FROM url WHERE  controller = '{$this->controller}' AND url = '{$url_unica}' AND id_controller != {$this->id} AND ativo = 1");

			if(!empty($query)){
				$url_unica = $this->url . '-' . $diferenciador;
				$diferenciador++;
			}else{
				$ja_existe = false;
			}
		}

		$this->url = $url_unica;
	}

	private function cadastrarUrl(){
		$insert_db = [
			'url'           => strtolower($this->url),
			'controller'    => $this->controller,
			'metodo'        => $this->metodo,
			'id_controller' => $this->id,
			'ativo'         => 1
		];

		return $this->model->insert_update(
			'url',
			['id_controller' => $this->id, 'controller' => $this->controller],
			$insert_db,
			true
		);
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function setUrl($url){
		$this->url = $url;
		return $this;
	}

	public function setController($controller){
		$this->controller = $controller;
		return $this;
	}

	public function setMetodo($metodo){
		$this->metodo = $metodo;
		return $this;
	}
}