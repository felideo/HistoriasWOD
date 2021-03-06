<?php
namespace Model;

class Post extends \Framework\Model{
	public function carregar_post($id = null, $where = null){
		$this->query
			->select('
				post.id,
				post.titulo,
				post.id_serie,
				post.id_livro,
				post.pagina,



				url.url,
				arquivo.endereco,
				arquivo.nome,
				arquivo.extensao,


				livro.titulo,
				livro.ano,
				livro.titulo_original,

				capa_livro.endereco,

				seo.robots,
				seo.revise,
				seo.title,
				seo.description,
				seo.keywords,
			')
			->from('post post')
			->leftJoin('url url ON url.id_controller = post.id AND controller = "post" AND url.ativo = 1')
			->leftJoin('arquivo arquivo ON arquivo.id = post.id_arquivo AND arquivo.ativo = 1')
			->leftJoin('livro livro ON livro.id = post.id_livro AND livro.ativo = 1')
			->leftJoin('arquivo capa_livro ON capa_livro.id = livro.id_arquivo AND capa_livro.ativo = 1')
			->leftJoin('seo seo ON seo.id_controller = post.id AND seo.controller = "post"')
			->where('post.ativo = 1')
			->orderBy('post.id_livro DESC, post.pagina ASC, post.id ASC');

		if(!empty($id)){
			$this->query
				->addSelect('post.post')
				->andWhere("post.id = '{$id}'");
		}else{
			$this->query
				->addSelect('SUBSTRING(post.post, 1, 127),');
		}

		if(!empty($where)){
			foreach ($where as $indice => $item){
				$this->query->andWhere("{$indice} = '{$item}'");
			}
		}

		$posts = $this->query->fetchArray();

		if(!isset($id) || empty($id)){
			foreach($posts as $indice => $post){
				$posts[$indice]['post'] = strip_tags($post['post']);
			}
		}

		return $posts;
	}

	public function carregar_ultimos_posts(){
		$this->query
			->select('
				post.id,
				post.titulo,
				post.pagina,

				livro.titulo,

				url.url,
			')
			->from('post post')
			->leftJoin('url url ON url.id_controller = post.id AND url.ativo = 1')
			->leftJoin('livro livro ON livro.id = post.id_livro AND livro.ativo = 1')
			->where('post.ativo = 1')
			->limitFrom(10)
			->orderBy('post.id DESC');

		return $this->query->fetchArray();
	}
}