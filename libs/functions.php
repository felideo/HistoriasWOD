<?php
function toText($msg){
    ob_start();
    echo "\n<pre>";

    if (is_array($msg)) {
        echo utf8_encode(print_r($msg, true));
    } else if (is_string($msg)) {
        echo "string(" . strlen($msg) . ") \"" . utf8_encode($msg) . "\"\n";
    } else if(is_object($msg)){
        echo "OBJECT \n\n";
        echo utf8_encode(print_r(var_export($msg), true));
    } else {
        echo var_dump($msg);
    }

    echo "\n";
    echo "</pre>";

    $msg = ob_get_clean();

    return $msg;
}

function carregar_trim($text) {
    if (is_string($text)) {
        return trim(preg_replace('/\s+/', ' ',$text));
    }

    if (is_array($text)) {
        foreach ($text as $k => $v) {
            $text[$k] = carregar_trim($text[$k]);
        }
        return $text;
    }

    if (is_object($text)) {
        $l = get_object_vars($text);
        foreach ($l as $k => $v) {
            $text->$k = carregar_trim($v);
        }
    }
}

function carregar_variavel($nome, $padrao = '') {
    if (isset($_POST[$nome])) {
        return transformar_array($_POST[$nome]);
    }

    if (isset($_GET[$nome])) {
        return transformar_array($_GET[$nome]);
    }

    if (isset($_FILES[$nome])) {
        return $_FILES[$nome];
    }

    $geral_get = explode('?', urldecode($_SERVER['REQUEST_URI']));

    if (isset($geral_get[1])) {
        $parametros_get = explode('&', $geral_get[1]);
        foreach ($parametros_get as $parametro) {
            $valor = explode('=', $parametro);

            if (count($valor) == 2) {
                if ($valor[0] == $nome) {
                    return $valor[1];
                }
            }
        }
    }

    return $padrao;
}

function transformar_array($variavel) {

    if (!is_array($variavel)) {
        return trim(preg_replace('/\s+/', ' ', $variavel));
    }

    foreach ($variavel as $chave => $cada) {

        if (is_array($cada)) {
            $variavel[$chave] = transformar_array($cada);
        } else {

            if (substr($chave, 0, 8) == 'numero__') {
                $variavel[substr($chave, 8)] = transformar_numero(trim(preg_replace('/\s+/', ' ', $cada)));
                unset($variavel[$chave]);
            } else if (substr($chave, 0, 6) == 'data__') {
                $variavel[substr($chave, 6)] = transformar_data(trim(preg_replace('/\s+/', ' ', $cada)));
                unset($variavel[$chave]);
            } else if (substr($chave, 0, 7) == 'senha__') {
                $variavel[substr($chave, 7)] = transformar_senha(trim(preg_replace('/\s+/', ' ', $cada)));
                unset($variavel[$chave]);
            }
        }
    }

    return $variavel;
}

function transformar_data($data) {

    $var = $data;

    $dataHora = explode(' ', $var);

    if (isset($dataHora[0])) {
        $data = explode('/', $dataHora[0]);

        if (count($data) != 3) {
            return $var;
        }

        $var = $data[2] . '-' . $data[1] . '-' . $data[0];

        if (isset($dataHora[1])) {
            $var .= ' ' . $dataHora[1];
        }
    }

    return $var;
}

function transformar_numero($numero, $forcar_verificacao = false) {
    if (is_numeric($numero) && !$forcar_verificacao) {
        return $numero;
    }

    if ($numero != '') {
        $var = $numero;
        $var = str_replace('R', '', $var);
        $var = str_replace('$', '', $var);
        $var = str_replace(' ', '', $var);
        $var = str_replace('.', '', $var);
        $var = str_replace(',', '.', $var);
    } else {
        return 0;
    }

    return $var;
}

function show_errors($show_erros = false){
	if(!empty($show_erros)){
		error_reporting(E_ALL);
		ini_set('display_startup_errors', 1);
		ini_set('display_errors', 1);

		return;
	}

	error_reporting(0);
	ini_set('display_startup_errors', 0);
    ini_set('display_errors', 0);

    return;
}