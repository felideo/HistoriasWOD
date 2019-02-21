<?php
namespace Model;
use Libs;

class Hierarquia extends \Framework\Model{

	public function carregar_listagem($busca, $datatable = null){
		$select = "SELECT"
			. " 	hierarquia.id,"
			. " 	hierarquia.nome,"
			. " 	hierarquia.nivel"

			. " FROM"
			. " 	hierarquia hierarquia"
			. " WHERE"
			. " 	hierarquia.ativo = 1";

		if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
			$select .= " AND hierarquia.id LIKE '%{$busca['search']['value']}%'";
			$select .= " OR hierarquia.nome LIKE '%{$busca['search']['value']}%'";
		}

		if(isset($busca['order'][0])){
			if($busca['order'][0]['column'] == 0){
				$select .= " ORDER BY hierarquia.id {$busca['order'][0]['dir']}";
			}elseif($busca['order'][0]['column'] == 1){
				$select .= " ORDER BY hierarquia.nome {$busca['order'][0]['dir']}";
			}
		}

		if(isset($busca['start']) && isset($busca['length'])){
			$select .= " LIMIT {$busca['start']}, {$busca['length']}";
		}

		return $this->select($select);
	}

	public function load_hierarquia($id){
		try {
			$query = $this->query;

			$query->select('
				hierarquia.id,
				hierarquia.nome,
				hierarquia.nivel,
				relacao.id,
				permissao.id
			')
			->from('hierarquia hierarquia')
			->leftJoin('hierarquia_relaciona_permissao relacao ON relacao.id_hierarquia = hierarquia.id AND relacao.ativo = 1')
			->leftJoin('permissao permissao ON permissao.id = relacao.id_permissao');

			$query->where("hierarquia.id = {$id}");

			$retorno = $query->fetchArray();
			$permissoes = [];

			foreach($retorno[0]['hierarquia_relaciona_permissao'] as $indice => $permissao){
				$permissoes[$permissao['permissao'][0]['id']] = $permissao;
			}

			$retorno[0]['hierarquia_relaciona_permissao'] = $permissoes;

			return $retorno[0];
		}catch(Exception $e){
            if (ERROS) throw new Exception('<pre>' . $e->getMessage() . '</pre>');

		}
	}
}
