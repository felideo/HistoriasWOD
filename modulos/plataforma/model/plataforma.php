<?php
namespace Model;

class Plataforma extends \Framework\Model {
	public function carregar_plataforma_pagina($id_plataforma, $publicado = null){
		if(is_array($id_plataforma)){
			$id_plataforma_pagina = $id_plataforma['id_plataforma_pagina'];
			$id_plataforma        = $id_plataforma['id_plataforma'];
		}

		$this->query->select('pagina.*')
			->from('plataforma_pagina pagina')
			->where("pagina.id_plataforma = {$id_plataforma} AND pagina.ativo = 1")
			->orderBy('pagina.ultima_atualizacao DESC');

		if(!empty($publicado)){
			$this->query->andWhere('pagina.publicado = 1');
		}

		if(!empty($id_plataforma_pagina)){
			$this->query->andWhere("pagina.id = {$id_plataforma_pagina}");
		}

		return $this->query->limit(1)
			->fetchArray();
	}

	public function carregar_listagem_historico($id_plataforma, $busca, $datatable = null){
		$this->query->select('
				pagina.id,
				pagina.id_usuario,
				pagina.ultima_atualizacao,
				pagina.publicado,
				pessoa.nome,
				pessoa.sobrenome,
			')
			->from('plataforma_pagina pagina')
			->leftJoin('pessoa pessoa ON pessoa.id_usuario = pagina.id_usuario AND pessoa.ativo = 1')
			->where("pagina.id_plataforma = {$id_plataforma} AND pagina.ativo = 1");

		if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
			$where = "pagina.id LIKE '%{$busca['search']['value']}%'"
				. " OR CONCAT(pessoa.nome, ' ', pessoa.sobrenome) LIKE '%{$busca['search']['value']}%'";

			$this->query->andWhere($where);
		}

		if(isset($busca['start']) && isset($busca['length'])){
			$this->query->limit($busca['length']);
			$this->query->offset($busca['start']);
		}

		if(isset($busca['order'][0]) && !empty($busca['order'][0])){
			switch($busca['order'][0]['column']){
				case '0':
					$this->query->orderBy("pagina.id {$busca['order'][0]['dir']}");
					break;

				case '1':
					$this->query->orderBy("CONCAT(pessoa.nome, ' ', pessoa.sobrenome) {$busca['order'][0]['dir']}");
					break;

				case '2':
					$this->query->orderBy("pagina.ultima_atualizacao {$busca['order'][0]['dir']}");
					break;

				case '3':
					$this->query->orderBy("pagina.publicado {$busca['order'][0]['dir']}");
					break;

				default:
					$this->query->orderBy("pagina.id {$busca['order'][0]['dir']}");
					break;
			}
		}

		return $this->query->fetchArray();
	}
}

