<?php
namespace Model;

use Libs;

class Modulo extends \Framework\Model{
	public function carregar_listagem($busca, $datatable = null){
		$this->query->select('
				modulo.id,
				modulo.nome,
				modulo.ordem,
				modulo.hierarquia,
				modulo.icone,
				submenu.nome_exibicao
			')
			->from('modulo modulo')
			->leftJoin('submenu submenu ON submenu.id = modulo.id_submenu AND submenu.ativo = 1')
			->where('modulo.ativo = 1');

		if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
			$where = "modulo.id LIKE '%{$busca['search']['value']}%'"
				. " OR modulo.nome LIKE '%{$busca['search']['value']}%'";

			$this->query->andWhere($where);
		}

		if(isset($busca['start']) && isset($busca['length'])){
			$this->query->limit($busca['length']);
			$this->query->offset($busca['start']);
		}

		if(isset($busca['order'][0]) && !empty($busca['order'][0])){
			switch($busca['order'][0]['column']){
				case '0':
					$this->query->orderBy("modulo.id {$busca['order'][0]['dir']}");
					break;

				case '1':
					$this->query->orderBy("modulo.nome {$busca['order'][0]['dir']}");
					break;

				case '2':
					$this->query->orderBy("modulo.ordem {$busca['order'][0]['dir']}");
					break;
			}
		}

		return [
			'dados' => $this->query->fetchArray(),
			'total' => $this->query->count()
		];
	}

	public function permissoes_basicas($modulo, $id_modulo){
		$permissoes_basicas = [
			'criar' => [
				'id_modulo' => $id_modulo,
				'permissao' => 'criar',
			],
			'visualizar' => [
				'id_modulo' => $id_modulo,
				'permissao' => 'visualizar',
			],
			'editar' => [
				'id_modulo' => $id_modulo,
				'permissao' => 'editar',
			],
			'deletar' => [
				'id_modulo' => $id_modulo,
				'permissao' => 'deletar',
			]
		];

		$erros = 0;

		foreach ($permissoes_basicas as $indice => $permissao) {
			$retorno[$indice] = $this->insert('permissao', $permissao);

			if(!empty($retorno[$indice]['id'])){
				$insert_relacao = [
					'id_hierarquia' => 1,
					'id_permissao'  => $retorno[$indice]['id']
				];

				$retorno_relacao[$indice] = $this->insert('hierarquia_relaciona_permissao', $insert_relacao);
			}


			$erros = !empty($retorno[$indice]['id']) ? $erros++ : $erros;

			$retorno[$indice]['erros'] = $erros;
		}

		return $retorno;
	}
}