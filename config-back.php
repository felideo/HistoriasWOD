<?php
 // header("Access-Control-Allow-Origin: *");

// Configuração do Fuso Horário
date_default_timezone_set('America/Sao_Paulo');

// if($_SERVER["HTTP_X_FORWARDED_PROTO"] != 'https' || substr($_SERVER['HTTP_HOST'], 0, 3) == 'www'){
// 	$redirect = 'https://' . ltrim($_SERVER['HTTP_HOST'], 'www.');

// 	if(isset($_GET['url']) && !empty($_GET['url'])){
// 		$redirect = rtrim($redirect, '/') . '/' . $_GET['url'];
// 	}

// 	Header('HTTP/1.1 301 Moved Permanently');
// 	Header('Location: ' . $redirect);
// 	exit;
// }

$protocolo = @$_SERVER["HTTP_X_FORWARDED_PROTO"] == 'https' ? "https://" : "http://";
$protocolo    = "http://";
$url          = $protocolo . $_SERVER["HTTP_HOST"] . "/";

// Sempre use barra (/) no final das URLs
define("URL", $url);

// Configuração com Banco de Dados
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'FelideoMVC');
define('DB_USER', 'felideo');
define('DB_PASS', 'lilith');

define('DEVELOPER', true);
define('PREVENT_CACHE', true);

define('APP_NAME', 'Felideo MVC');

define('EMAIL_EMAIL', 'felideo@ggmail.com');
define('EMAIL_SENHA', '');
