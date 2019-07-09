<?php
namespace Framework;

class URL{
	public static function get_url(){
		return filter_var(self::get_scheme() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
	}

	private static function get_scheme(){
		$scheme = "http";

		if(isset($_SERVER["HTTP_X_FORWARDED_PROTO"])){
			return $_SERVER["HTTP_X_FORWARDED_PROTO"];
		}

		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
			return 'https';
		}

		return $scheme;
	}

	private static function get_url_parsed(){
		return parse_url(self::get_url());
	}
}