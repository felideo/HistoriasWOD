<?php
namespace Model;

class Livro extends \Framework\Model{
	public function carregar_livro($id = null){
		$this->query->select('
				livro.titulo, livro.titulo_original, livro.ano,
				arquivo.endereco,
				url.url
			')
			->from('livro livro')
			->leftJoin('arquivo arquivo ON arquivo.id = livro.id_arquivo AND arquivo.ativo = 1')
			->leftJoin('url url ON url.id_controller = livro.id AND controller = "livro" AND url.ativo = 1')
			->where('livro.ativo = 1');

		if(isset($id) && !empty($id)){
			$this->query->addselect("livro.id = '{$id}'");
		}

		return $this->query->fetchArray();
	}
}