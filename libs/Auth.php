<?php
namespace Libs;

class Auth {
	public static function handLeLoggin() {
		if(!isset($_SESSION) || empty($_SESSION['logado'])){
			header('location: ' . Redirect::getUrl());
			exit;
		}
	}

	public static function esta_logado(){
		return isset($_SESSION) && !empty($_SESSION['logado']);
	}
}