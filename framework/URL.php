<?php
namespace Framework;

class URL{
	private $url;
	private $parsed;
	private $scheme;

	public function get_url(){
		if(!empty($this->url)){
			return $this->url;
		}

		$this->url = filter_var($this->get_scheme() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

		return $this->url;
	}

	public function get_parsed(){
		if(!empty($this->parsed)){
			return $this->parsed;
		}

		if(empty($this->url)){
			$this->get_url();
		}

		$this->parsed = parse_url($this->url);
		return $this->parsed;
	}

	public function get_scheme(){
		if(!empty($this->scheme)){
			return $this->scheme;
		}

		$this->scheme = "http";

		if(isset($_SERVER["HTTP_X_FORWARDED_PROTO"])){
			$this->scheme = $_SERVER["HTTP_X_FORWARDED_PROTO"];
			return $_SERVER["HTTP_X_FORWARDED_PROTO"];
		}

		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
			$this->scheme = 'https';
			return 'https';
		}

		return $this->scheme;
	}

}