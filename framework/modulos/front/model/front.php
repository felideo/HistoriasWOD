<?php
namespace Model;

use Libs;

class Front extends \Framework\Model{
	public function carregar_paginas_institucionais(){
		return $this->query
			->select('
				pagina.id,
				pagina.titulo,
				pagina.exibir_cabecalho,
				pagina.exibir_rodape,
				url.url,
				url.controller
			')
			->from('pagina_institucional pagina')
			->leftJoin("url url ON url.id_controller = pagina.id AND url.controller = 'pagina_institucional' AND url.ativo = 1")
			->where('pagina.ativo = 1')
			->fetchArray();
	}

	public function carregar_banners(){
		$query = $this->query;
		$query->select('
			banner.ordem,
			arquivo.nome,
			arquivo.endereco,
			arquivo.extensao,
		')
		->from('banner banner')
		->leftJoin('arquivo arquivo ON arquivo.id = banner.id_arquivo and arquivo.ativo = 1')
		->orderBy('banner.ordem ASC')
		->where("banner.ativo = 1");

		return $query->fetchArray();
	}
}