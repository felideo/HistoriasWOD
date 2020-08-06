<?php
namespace ModelCore;

class Sitemap extends \Framework\Model{
	public function model_carregar_paginas(){
		return $this->query->select('
				url.*
			')
			->from('url url')
			->where('url.ativo = 1')
			->fetchArray();
	}
}