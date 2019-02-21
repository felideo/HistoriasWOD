<?php
// Configuração do Fuso Horário
date_default_timezone_set('America/Sao_Paulo');

$protocolo = !empty($_SERVER["HTTPS"]) ? "https://" : "http://";
$url       = $protocolo . $_SERVER["HTTP_HOST"] . "/";

// Sempre use barra (/) no final das URLs
define("URL", $url);

// Configuração com Banco de Dados
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'FelideoMVC');
define('DB_USER', 'root');
define('DB_PASS', 'lilith');

define('DEVELOPER', true);
define('PREVENT_CACHE', true);

define('APP_NAME', 'Felideo MVC');

if(function_exists('xdebug_disable')){
		xdebug_disable();
}

define('EMAIL_EMAIL', '');
define('EMAIL_SENHA', '');
