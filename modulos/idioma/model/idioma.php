<?php
namespace Model;

use Libs;

class Idioma extends \Framework\Model{
	public function __construct() {
		parent::__construct();
	}

	public function carregar_listagem($busca, $datatable = null){
		$select = "SELECT"
			. " 	idioma.id,"
			. " 	idioma.idioma"
			. " FROM"
			. " 	idioma idioma"
			. " WHERE"
			. " 	idioma.ativo = 1";

		if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
			$select .= " AND idioma.idioma LIKE '%{$busca['search']['value']}%'";
		}

		if(isset($busca['order'][0])){
			if($busca['order'][0]['column'] == 0){
				$select .= " ORDER BY idioma.id {$busca['order'][0]['dir']}";
			}elseif($busca['order'][0]['column'] == 1){
				$select .= " ORDER BY idioma.idioma {$busca['order'][0]['dir']}";
			}
		}

		if(isset($busca['start']) && isset($busca['length'])){
			$select .= " LIMIT {$busca['start']}, {$busca['length']}";
		}

		return $this->select($select);
	}

	public function buscar_idioma($busca){
		$select = "SELECT"
			. " 	idioma.id,"
			. " 	idioma.idioma"
			. " FROM"
			. " 	idioma idioma"
			. " WHERE"
			. " 	idioma.idioma LIKE '%{$busca['nome']}%'"
			. " AND idioma.ativo = 1";

		if(isset($busca['page_limit'])){
			$select .= " LIMIT {$busca['page_limit']}";
		}

		return $this->select($select);
	}
}