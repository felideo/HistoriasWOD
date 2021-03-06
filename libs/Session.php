<?php
namespace Libs;

class Session {
	public function init(){
		@session_start();
	}

	public function set($key, $value = false){
		$_SESSION[$key] = $value;
	}

	public function get($key){
		if(isset($_SESSION[$key])){
			return $_SESSION[$key];
		}

		return false;
	}

	public function remove($key){
		if(isset($_SESSION[$key])){
			unset($_SESSION[$key]);
		}
	}

	public function destroy(){
		unset($_SESSION);
		session_destroy();
	}
}