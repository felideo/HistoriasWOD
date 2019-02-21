<?php
namespace Libs;

class GerarBotoes{

	private $botoes = [];

	private	$id;
	private	$class;
	private	$href;
	private	$value;
	private	$data = [];
	private	$onClick;
	private	$title;
	private $texto_livre;
	private $permissao;
	private $inside_li;
	private $texto;
	private $bloqueado;

	public function __construct() {
		$this->botoes = [];
	}

	public function setID($id){
		$this->id = $id;
		return $this;
	}

	public function setClass($class){
		$this->class = $class;
		return $this;
	}


	public function setHref($href){
		$this->href = $href;
		return $this;
	}

	public function setValue($value){
		$this->value = $value;
		return $this;
	}

	public function setData($data){
		$this->data[] = $data;
		return $this;
	}

	public function setOnClick($onClick){
		$this->onClick = $onClick;
		return $this;
	}

	public function setTitle($title){
		$this->title = $title;
		return $this;
	}

	public function setTextoLivre($texto_livre){
		$this->texto_livre = $texto_livre;
		return $this;
	}

	public function setTexto($texto){
		$this->texto = $texto;
		return $this;
	}

	public function setPermissao($permissao){
		$this->permissao = $permissao;
		return $this;
	}

	public function setBloqueado($bloqueado){
		$this->bloqueado = $bloqueado;
		return $this;
	}

	public function setInsideLi($inside_li){
		$this->inside_li = $inside_li;
		return $this;
	}

	public function gerarBotao(){
		if(!isset($this->permissao) || empty($this->permissao) || $this->permissao == 0){
			$this->unset_attrs();
			return;
		}

		$index = 0;

		if(isset($this->botoes) && !empty($this->botoes) && is_array($this->botoes)){
			$index = count($this->botoes) + 1;
		}


		$disabled   = !empty($this->bloqueado) ? ' disabled ' : ' ';

		if(isset($this->href) && !empty($this->href)){
			$this->href = !empty($this->bloqueado) ? null : $this->href;
		}

		if(isset($this->texto_livre) && !empty($this->texto_livre) && !empty($this->bloqueado)
			&& (preg_match("/" . "target='_blank'" . "/", $this->texto_livre) || preg_match('/' . 'target="_blank"' . '/', $this->texto_livre))
		){
			$this->texto_livre = str_replace("target='_blank'", ' ', $this->texto_livre);
			$this->texto_livre = str_replace('target="_blank"', ' ', $this->texto_livre);
		}

		$this->botoes[$index] = [
			'id'      		=> !empty($this->id)      	    ? " id='{$this->id}' "      		: ' ',
			'class'   		=> !empty($this->class)   	    ? " class='{$this->class}' "   		: ' ',
			'href'    		=> !empty($this->href)    	    ? " href='{$this->href}' "    		: ' href="javascript:;" ',
			'value'   		=> !empty($this->value)   	    ? " value='{$this->value}' "   		: ' ',
			'onClick' 		=> !empty($this->onClick) 	    ? " onClick='{$this->onClick}' " 	: ' ',
			'title'   		=> !empty($this->title)   	    ? " title='{$this->title}' "   		: ' ',
			'texto_livre'   => !empty($this->texto_livre)   ? " {$this->texto_livre} "   		: ' ',
			'inside_li'   	=> !empty($this->inside_li)   	? true   							: false,
			'texto'   		=> !empty($this->texto)   		? "{$this->texto}"	 				: '',

		];


		if(isset($this->data) && !empty($this->data) && is_array($this->data)){
			$this->botoes[$index]['data'] = '';

			foreach ($this->data as $indice => $data_item) {
				$this->botoes[$index]['data'] .= !empty($data_item) ? " data-{$data_item} " : ' ';
			}
		}

		$this->unset_attrs();
	}

	public function getBotoes(){
		$retorno = '';

		if(!isset($this->botoes) || empty($this->botoes)){
			$this->clean_class();
			return '';
		}

		foreach ($this->botoes as $indice => $botao) {
			$texto = isset($this->botoes[$indice]['texto']) && !empty($this->botoes[$indice]['texto']) ? $this->botoes[$indice]['texto'] : '';

			if(isset($this->botoes[$indice]['inside_li']) && !empty($this->botoes[$indice]['inside_li'])){
				unset($this->botoes[$indice]['inside_li']);
				unset($this->botoes[$indice]['texto']);

				$retorno .= '<li><a ' . implode(' ', $this->botoes[$indice]) . ' >' . $texto . '</a></li> ';
				continue;
			}

			unset($this->botoes[$indice]['texto']);

			$retorno .= '<a ' . implode(' ', $this->botoes[$indice]) . ' >' . $texto . '</a> ';
			unset($texto);
		}

		$this->clean_class();
		return $retorno;
	}

	private function clean_class(){
		foreach ($this as &$value) {
		    $value = null;
		}
	}

	private function unset_attrs(){
		unset($this->class);
		unset($this->id);
		unset($this->href);
		unset($this->value);
		unset($this->data);
		unset($this->onClick);
		unset($this->title);
		unset($this->bloqueado);
		unset($this->texto_livre);
		unset($this->texto);
	}

}