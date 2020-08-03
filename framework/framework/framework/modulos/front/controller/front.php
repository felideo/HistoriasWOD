<?php
namespace ControllerCore;

class Front extends \Framework\Controller {
	protected $modulo = [
		'modulo' 	=> 'front',
		'name'		=> 'Front',
		'send'		=> 'Front'
	];

	public function carregar_cabecalho_rodape(){
		$this->view->processar_includes(['cabecalho', 'navbar', 'rodape']);
		$this->carregar_paginas_institucionais();
		$this->carregar_banners();
	}

	public function montar_breadcrumb($breadcrumb){
		$retorno = '';

		foreach($breadcrumb as $indice => $item){
			$active = false;
			$href   = $item[0];

			if(isset($item[2]) && !empty($item[2])){
				$active = true;
			}

			if(isset($item[1]) && !empty($item[1])){
				$href = "<a href='{$item[1]}'>{$item[0]}</a>";
			}


			$retorno .= "\n<li class='breadcrumb-item " . (!empty($active) ? " active " : "") . "'> " . $href . "</li>";
		}

		return $retorno;
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