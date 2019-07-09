<?php
namespace Model;

class Produto extends \Framework\Model{
	public function carregar_cadastro($id = null){
		$query = $this->query;
		$query->select('
			produto.*,
			arquivo.*,
		')
		->from('produto produto')
		->leftJoin('arquivo arquivo ON arquivo.id = produto.id_arquivo and arquivo.ativo = 1')
		->where("produto.ativo = 1");

		if(isset($id) && !empty($id)){
			$query->andWhere("produto.id = {$id}");
		}

		return $query->fetchArray();
	}
}
