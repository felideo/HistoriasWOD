<?php
namespace ControllerCore;

class Sitemap extends \Framework\Controller {
	protected $modulo = [
		'modulo' 	=> 'sitemap'
	];

	public function index(){
		$url     = $this->universe->get_url();
		$url     = "{$url['scheme']}://{$url['host']}";
		$paginas = $this->model->model_carregar_paginas();
		$sitemap = new \samdark\sitemap\Sitemap($_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml');

		$sitemap->addItem("{$url}", time(), \samdark\sitemap\Sitemap::DAILY, 0.1);
		$sitemap->addItem("{$url}/sitemap", time(), \samdark\sitemap\Sitemap::HOURLY, 0.1);

		foreach($paginas as $indice => $pagina){
			$index = $pagina['url'];

			if(!empty($pagina['controller'])){
				$index = "{$pagina['controller']}/{$pagina['url']}";
			}

			$sitemap->addItem("{$url}/{$index}", time(), \samdark\sitemap\Sitemap::DAILY, 0.3);
		}

		$sitemap->write();

		echo 'Ok';
		exit;
	}
}