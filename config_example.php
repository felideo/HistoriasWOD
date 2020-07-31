<?php
// Configuração do Fuso Horário
date_default_timezone_set('America/Sao_Paulo');

// Configuração com Banco de Dados
define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'SWDB');
define('DB_USER', 'root');
define('DB_PASS', '12345');

define('DEVELOPER', true);
define('PREVENT_CACHE', false);
define('LOCALIZADO_QUERY', false);

define('APP_NAME', 'Scientific Work DB');

if(function_exists('xdebug_disable')){
		xdebug_disable();
}

define('EMAIL_EMAIL', '');
define('EMAIL_SENHA', '');

define('DEPLOY_KEY', 'ce85b99cc46752fffee35cab9a7b0278abb4c2d2055cff685af4912c49490f8d');
define('DEPLOY_FOLDER', '/www/pieces_of_a_crypto_mystery/');