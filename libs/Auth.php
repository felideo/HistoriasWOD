<?php
namespace Libs;

class Auth {
	public function is_logged($redirect = true){
		if(empty($redirect)){
			return isset($_SESSION) && !empty($_SESSION['logado']);
		}

		if(!isset($_SESSION) || empty($_SESSION['logado']) || empty($_SESSION['usuario']['acesso_admin'])){
			header('location: /');
			exit;
		}
	}
}