<?php
namespace Model;

class Livro extends \Framework\Model{
	public function carregar_livro($id = null){
		$this->query->select('
				livro.titulo, livro.titulo_original, livro.ano,
				arquivo.endereco,
				arquivo.nome,
				url.url,
				post.titulo,
				post.pagina,
				url_post.url
			')
			->from('livro livro')
			->leftJoin('arquivo arquivo ON arquivo.id = livro.id_arquivo AND arquivo.ativo = 1')
			->leftJoin('url url ON url.id_controller = livro.id AND url.controller = "livro" AND url.ativo = 1')
			->leftJoin('post post ON post.id_livro = livro.id AND post.ativo = 1')
			->leftJoin('url url_post ON url_post.id_controller = post.id AND url_post.controller = "post" AND url_post.ativo = 1')
			->where('livro.ativo = 1')

			->orderBy('livro.ano ASC, post.pagina ASC');

		if(isset($id) && !empty($id)){
			$this->query->addselect("livro.id = '{$id}'");
		}

		return $this->query->fetchArray();
	}
}
