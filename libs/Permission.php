<?php
namespace Libs;

class Permission {
	private $universe;

	public function check($modulo, $permissao) {
		if(!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])){
			$this->redirect();
		}

		if($_SESSION['usuario']['super_admin'] != 1 && (empty($_SESSION['permissoes'][$modulo]) || empty($_SESSION['permissoes'][$modulo][$permissao]))){
			$this->redirect();
		}
	}

	public function check_user_permission($modulo, $permissao) {
		if($_SESSION['usuario']['super_admin'] != 1 && (empty($_SESSION['permissoes'][$modulo]) || empty($_SESSION['permissoes'][$modulo][$permissao]))){
			return false;
		}

		return true;
	}

	private function redirect(){
		$this->universe = \Framework\Universe::get_universe();
		$this->universe->get_view()->alert_js('Vocã não possui permissão para efetuar esta ação...', 'erro');
		header('location: /acesso/admin');
		exit;
	}
}