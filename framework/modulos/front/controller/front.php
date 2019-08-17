<?php
namespace Controller;

use Libs;

class Front extends \Framework\Controller {
	protected $modulo = [
		'modulo' 	=> 'front',
		'name'		=> 'Front',
		'send'		=> 'Front'
	];

	public function carregar_cabecalho_rodape(){

		$this->carregar_paginas_institucionais();
		$this->carregar_banners();
	}

	private function carregar_paginas_institucionais(){
		$paginas_institucionais = $this->model->carregar_paginas_institucionais();

		if(empty($paginas_institucionais)){
			$this->view->assign('paginas_institucionais', []);
			return;
		}

		$contador = 0;

		$tmp = [];

		foreach ($paginas_institucionais as $indice => $item) {
			switch ($contador) {
				case 0:
					$tmp[0][] = $item;
					break;
				case 1:
					$tmp[1][] = $item;
					break;
				case 2:
					$tmp[2][] = $item;
					$contador = -1;
					break;
			}

			$contador++;
		}

		$paginas_institucionais = $tmp;
		$this->view->assign('paginas_institucionais', $paginas_institucionais);
	}

	private function carregar_banners(){
		$banners = $this->model->carregar_banners();

		if(empty($banners)){
			$this->view->assign('banners', []);
			return;
		}

		$this->view->assign('banners', $banners);
	}
}