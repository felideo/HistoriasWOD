<?php
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

require 'framework/Error.php';
require 'libs/functions.php';

if(file_exists('config.php')){
	require 'config.php';
}

require 'vendor/autoload.php';

if((!defined('DEVELOPER') || empty(DEVELOPER)) &&
	(!isset($_SESSION['mostrar_erros']) || empty($_SESSION['mostrar_erros'])) &&
	(isset($_SESSION['mostrar_erros']) && $_SESSION['mostrar_erros'] != 'habilitado')
){
	error_reporting(0);
	ini_set('display_startup_errors', 0);
    ini_set('display_errors', 0);
}

\Libs\Session::init();
new Framework\BigBang();
