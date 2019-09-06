<?php
namespace Controller;

class Cron extends \Framework\Controller {
	public function index(){
		$crons = $this->model->get_crons();

		if(empty($crons)){
			debug2('Sem crons a serem executador');
			exit;
		}

		foreach($crons as $indice => $cron){
			$retorno_cron = $this->model->update(
				'gerenciador_cron',
				['horario' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . $cron['frequencia']))],
				['id' => $cron['id']]
			);

			$controller = $this->universe->get_controller($cron['modulo']);
			$metodo     = $cron['metodo'];
			$parametros = explode('/', $cron['parametros']);
			$controller->{$metodo}($parametros);
		}
	}
}