<?php
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

@session_start();

require 'libs/Performance.php';
performance_start();

require 'framework/Fail.php';
require 'libs/functions.php';

if(file_exists('config.php')){
	require 'config.php';
}

require 'vendor/autoload.php';

show_errors(
	(defined('DEVELOPER') && !empty(DEVELOPER))
	|| (isset($_SESSION['mostrar_erros']) && $_SESSION['mostrar_erros'] == 'habilitado')
);

try{
	$big_bang = new Framework\BigBang();
	$big_bang->expanse();
}catch(\Fail $e){
	$e->show_errors();
}
