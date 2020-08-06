<?php
if(isset($_SERVER['HTTP_VIA']) && !empty($_SERVER['HTTP_VIA']) && !empty(stripos($_SERVER['HTTP_VIA'], 'archive'))){
	http_response_code(418);
	echo '<strong>418.</strong> I’m a teapot.';
	exit;
}

if(!empty(stripos($_SERVER['HTTP_USER_AGENT'], 'archive'))){
	http_response_code(418);
	echo '<strong>418.</strong> I’m a teapot.';
	exit;
}

// if((!isset($_SERVER['HTTP_ORIGIN']) || empty($_SERVER['HTTP_ORIGIN'])) && $_SERVER['REQUEST_METHOD'] != 'GET'){
// 	header('Location: /error');
// 	exit;
// }

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

@session_start();
require 'vendor/autoload.php';

performance_start();

require 'framework/Fail.php';
require 'libs/functions.php';

if(file_exists('config.php')){
	require 'config.php';
}

show_errors(true);

try{
	$big_bang = new Framework\BigBang();
	$big_bang->inflate();
}catch(\Exception $e){
	show_error($e);
}catch(\PDOException $e){
	show_error($e);
}catch(\Error $e){
	show_error($e);
}catch(\Fail $e){
	$e->show_error();
}
