<?php
namespace Libs;

class Auth {
	public static function handLeLoggin($redirect = null) {
		if(!isset($_SESSION) || empty($_SESSION['logado'])){
			if(!empty($redirect)){
				header('location: ' . $redirect);
				exit;
			}

			header('location: ' . Redirect::getUrl());
			exit;
		}
	}

	public static function esta_logado(){
		return isset($_SESSION) && !empty($_SESSION['logado']);
	}
}