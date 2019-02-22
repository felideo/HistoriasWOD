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

show_errors(
		(defined('DEVELOPER') && !empty(DEVELOPER))
	||  (isset($_SESSION['mostrar_erros']) && $_SESSION['mostrar_erros'] == 'habilitado')
);

\Libs\Session::init();
new Framework\BigBang();
