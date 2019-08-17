<?php
namespace Model;

use Libs;

class Permissao extends \Framework\Model{
	public function carregar_listagem($busca, $datatable){
		$query = $this->query;

		$query->select('
			permissao.id,
			permissao.permissao,
			modulo.nome
		')
		->from('permissao permissao')
		->leftJoin('modulo modulo ON modulo.id = permissao.id_modulo')
		->where('permissao.ativo = 1');

		if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
			$query->where("permissao.id LIKE '%{$busca['search']['value']}%'", 'AND')
				->where("permissao.permissao LIKE '%{$busca['search']['value']}%'", 'OR')
				->where("modulo.nome LIKE '%{$busca['search']['value']}%'", 'OR');
		}

		if(isset($busca['order'][0])){
			if($busca['order'][0]['column'] == 0){
				$query->orderBy("permissao.id {$busca['order'][0]['dir']}");
			}elseif($busca['order'][0]['column'] == 1){
				$query->orderBy("modulo.nome {$busca['order'][0]['dir']}");
			}elseif($busca['order'][0]['column'] == 2){
				$query->orderBy("permissao.permissao {$busca['order'][0]['dir']}");
			}
		}


		if(isset($busca['start']) && isset($busca['length'])){
			$query->limit($busca['length'])
				->offset($busca['start']);
		}

		return $query->fetchArray();
	}

	public function load_permissions_list() {
		$select = 'SELECT permissao.*,'
			. '  modulo.id as modulo_id, modulo.nome as modulo_nome, modulo.icone as modulo_icone, modulo.modulo as modulo_modulo, modulo.hierarquia as modulo_hierarquia'
			. ' FROM permissao permissao'
			. ' LEFT JOIN modulo modulo'
			. ' ON modulo.id = permissao.id_modulo'
			. ' WHERE modulo.ativo = 1 AND permissao.ativo = 1';


		$permissoes = $this->select($select);

		foreach($permissoes as $indice => $permissao) {
			if(!isset($retorno[$permissao['modulo_modulo']])){
				$retorno[$permissao['modulo_modulo']] = [
					'modulo' => [
						'id' => $permissao['modulo_id'],
						'modulo' => $permissao['modulo_modulo'],
						'nome' => $permissao['modulo_nome'],
						'icone' => $permissao['modulo_icone']
					]
				];
			}

			$retorno[$permissao['modulo_modulo']]['permissoes'][$permissao['permissao']] = [
				'id' => $permissao['id'],
	            'permissao' => $permissao['permissao'],
	            'nome'	=> str_replace('_', ' ', str_replace($permissao['modulo_modulo'] . '_', '', $permissao['permissao']))
			];
		}

		return $retorno;
	}
}
