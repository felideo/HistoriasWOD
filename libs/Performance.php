<?php

function performance_start($acha_facil = false){
    unset($_SESSION['performance_test']);
    $_SESSION['performance_test']['acha_facil'] = $acha_facil;

    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

    $_SESSION['performance_test'] = [
        'start'        => microtime(true),
        'memory_start' => memory_get_peak_usage(true),
        'place'        => [
            'start' => [
                'class'    => isset($backtrace[1]['class']) ? $backtrace[1]['class'] : '',
                'line'     => $backtrace[0]['line'],
                'function' => isset($backtrace[1]) ? $backtrace[1]['function'] : $backtrace[0]['function'],
                'file'     => $backtrace[0]['file']
            ]
        ]
    ];
}

function performance_stop($print_acha_facil = false){
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

        debug2($erro, $_SESSION['performance_test']['acha_facil']);
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

    $hours        = round(($duration / 60 / 60), 2);
    $minutes      = round(($duration / 60) - $hours * 60, 2);
    $seconds      = round($duration - $hours * 60 * 60 - $minutes * 60, 2);
    // $microseconds = (float) ($duration - $seconds - ($minutes * 60));
    $milisegundos = round($_SESSION['performance_test']['duration'] * 1000, 2);

    $_SESSION['performance_test']['duration'] = [
        'horas'        => $hours,
        'minutos'      => $minutes,
        'segundos'     => $seconds,
        'milisegundos' => $milisegundos,
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
    unset($_SESSION['performance_test']);

}