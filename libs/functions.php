<?php

//Por Felideo Oficial!
function debug2($var, $legenda = false, $exit = false) {
    //Se for ajax deve ser exibido em JSON FORMAT
    // if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    //     if(is_array(carregar_UTF8($var))){
    //         echo json_encode(carregar_UTF8($var));
    //     }else{
    //         echo json_encode(array(carregar_UTF8($var)));
    //     }

    // }else{

        echo "\n<pre style='position: relative; z-index: 99999;''>";
        echo "============================ DEBUG2 OFICIAL ==========================\n";



        foreach($GLOBALS as $var_name => $value) {
            if ($value === $var) {

                $variavel = "Variavel => $" . $var_name;

                $tamanho = strlen ($variavel);
                $tabs = str_repeat('&nbsp;', (70 - $tamanho) / 2);
                echo $tabs . $variavel . "\n";
            }
        }

        if ($legenda){
            $legenda = strtoupper($legenda);
            $tamanho = strlen ($legenda);
            $tabs = str_repeat('&nbsp;', (70 - $tamanho) / 2);
            echo $tabs . $legenda . "\n\n";
        }
        if (is_array($var) || is_object($var)) {
            echo htmlentities(print_r($var, true));
        } elseif (is_string($var)) {
            echo "string(" . strlen($var) . ") \"" . htmlentities($var) . "\"\n";
        } else {
            var_dump($var);
        }
        // echo "\n=============== FIM ===============\n";
        echo "\n";
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo "\n";

        echo "</pre>";
    // }

    if ($exit) {
        die;
    }
}

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

function remover_acentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}



function performance_start($acha_facil = null){
    if(!empty($acha_facil)){
        $_SESSION['performance_test']['acha_facil'] = $acha_facil;
    }

    if(isset($_SESSION['performance_test']) && is_array($_SESSION['performance_test'])){
        performance_stop();
    }

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

    $_SESSION['performance_test'] = [
        'start'        => microtime(true),
        'memory_start' => memory_get_peak_usage(true),
        'place'        => [
            'start' => [
                'class'    => isset($backtrace[1]['class']) ? $backtrace[1]['class'] : '',
                'line'     => $backtrace[0]['line'],
                'function' => $backtrace[1]['function'],
                'file'     => $backtrace[0]['file']
            ]
        ]
    ];
}

function performance_stop(){
    $print_acha_facil = !empty($_SESSION['performance_test']['acha_facil']) ? $_SESSION['performance_test']['acha_facil'] : null;

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $index_backtrace = [0, 1];

    if($backtrace[1]['function'] == 'performance_start'){
        $index_backtrace = [1, 2];
    }

    if(!isset($_SESSION['performance_test']['start']) || !is_array($_SESSION['performance_test'])){
        $erro = [
            'error' => 'Obrigatorio efetuar a chamada de performance_start() anterior a performance_stop()',
            'place' => [
                'end' => [
                    'class'    => isset($backtrace[$index_backtrace[1]]['class']) ? $backtrace[$index_backtrace[1]]['class'] : '',
                    'line'     => $backtrace[$index_backtrace[0]]['line'],
                    'function' => $backtrace[$index_backtrace[1]]['function'],
                    'file'     => $backtrace[$index_backtrace[0]]['file']
                ]
            ]
        ];

        debug2($erro, $print_acha_facil);
        exit;
    }

    $_SESSION['performance_test'] += [
        'end'        => microtime(true),
        'memory_end' => memory_get_peak_usage(true)
    ];

    $_SESSION['performance_test']['duration'] = $_SESSION['performance_test']['end'] - $_SESSION['performance_test']['start'];



    $_SESSION['performance_test']['place']['end'] = [
        'class'    => isset($backtrace[$index_backtrace[1]]['class']) ? $backtrace[$index_backtrace[1]]['class'] : '',
        'line'     => $backtrace[$index_backtrace[0]]['line'],
        'function' => $backtrace[$index_backtrace[1]]['function'],
        'file'     => $backtrace[$index_backtrace[0]]['file']
    ];

    $duration =  $_SESSION['performance_test']['end'] - $_SESSION['performance_test']['start'];

    $hours        = (int) ($duration / 60 / 60);
    $minutes      = (int) ($duration / 60) - $hours * 60;
    $seconds      = (int) $duration - $hours * 60 * 60 - $minutes * 60;
    $microseconds = (float) ($duration - $seconds - ($minutes * 60));

    $_SESSION['performance_test']['duration'] = [
        'horas'         => $hours,
        'minutos'       => $minutes,
        'segundos'      => $seconds,
        'microsegundos' => $microseconds
    ];

    $retorno = [
        'memory_start' => $_SESSION['performance_test']['memory_start'] / 1048576 . ' Mb',
        'memory_end'   => $_SESSION['performance_test']['memory_end'] / 1048576 . ' Mb',
        'memory_usage' => ($_SESSION['performance_test']['memory_end'] - $_SESSION['performance_test']['memory_start']) / 1048576 . ' Mb',
        'place'        => $_SESSION['performance_test']['place'],
        'duration'     => $_SESSION['performance_test']['duration'],
    ];

    unset($_SESSION['performance_test']);

    debug2($retorno, $print_acha_facil);
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
