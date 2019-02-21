<?php
namespace Libs;

class Redirect {
	public static function getUrl(){
		$redirect = '/error';

		if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
			$redirect = $_SERVER['HTTP_REFERER'];
		}

		return $redirect;
	}
}