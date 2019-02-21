<?php
namespace Libs;

class Permission {
	public static function check($modulo, $permissao) {
		if($_SESSION['usuario']['super_admin'] != 1 && (empty($_SESSION['permissoes'][$modulo]) || empty($_SESSION['permissoes'][$modulo][$permissao]))){
			$view = new \Libs\View();
			$view->alert_js('Vocã não possui permissão para efetuar esta ação...', 'erro');
			header('location: ' . Redirect::getUrl());
			exit;
		}
	}

	public static function check_user_permission($modulo, $permissao) {
		if($_SESSION['usuario']['super_admin'] != 1 && (empty($_SESSION['permissoes'][$modulo]) || empty($_SESSION['permissoes'][$modulo][$permissao]))){
			return false;
		}

		return true;
	}
}