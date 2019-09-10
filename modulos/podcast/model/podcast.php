<?php
namespace Model;

class Podcast extends \Framework\Model{
	public function carregar_podcast($id){
		return $this->query->select('
				podcast.nome,
				podcast.descricao,
				podcast.link,
				podcast.data_inicio,
				podcast.data_cadastro,
				idioma.idioma,
			')
			->from('podcast podcast')
			->leftJoin('idioma idioma'
				. ' ON idioma.id = podcast.id_idioma'
			)
			->where("podcast.id = {$id}")
			->fetchArray('first')[0];
	}
}