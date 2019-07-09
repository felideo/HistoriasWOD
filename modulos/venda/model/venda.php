<?php
namespace Model;
use Libs;

class Venda extends \Framework\Model{
	public function carregar_listagem($busca, $datatable = null){
		$query = $this->query;
		$query->select('
			venda.*,
			usuario.*,
			pessoa.*,
			produto.*,
		')
		->from('venda venda')
		->leftJoin('usuario usuario ON usuario.id = venda.id_usuario')
		->leftJoin('pessoa pessoa ON pessoa.id_usuario = usuario.id and pessoa.ativo = 1')
		->leftJoin('produto produto ON produto.id = venda.id_produto')
		->where('venda.ativo = 1');

		// if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
		// 	$query->andWhere("
		// 		banner.id LIKE '%{$busca['search']['value']}%'
		// 		OR banner.ordem LIKE '%{$busca['search']['value']}%'
		// 		OR arquivo.nome LIKE '%{$busca['search']['value']}%'
		// 	", 'AND');
		// }

		if(isset($busca['order'][0])){
			if($busca['order'][0]['column'] == 0){
				$query->orderBy("venda.id {$busca['order'][0]['dir']}");
			}

			// elseif($busca['order'][0]['column'] == 1){
			// 	$query->orderBy("banner.ordem {$busca['order'][0]['dir']}");
			// }elseif($busca['order'][0]['column'] == 2){
			// 	$query->orderBy("arquivo.nome {$busca['order'][0]['dir']}");
			// }
		}

		$retorno = $query->fetchArray();

		return $retorno;
	}

	public function carregar_banner($id){
		$query = $this->query;
		$query->select('
			banner.ordem,
			banner.id_arquivo,
			arquivo.nome,
			arquivo.endereco,
			arquivo.extensao,
		')
		->from('banner banner')
		->leftJoin('arquivo arquivo ON arquivo.id = banner.id_arquivo and arquivo.ativo = 1')
		->where("banner.ativo = 1 AND banner.id = {$id}");

		return $query->fetchArray();
	}
}
