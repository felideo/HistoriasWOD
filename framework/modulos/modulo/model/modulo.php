<?php
namespace Model;

use Libs;

class Modulo extends \Framework\Model{
	public function load_modulo_list(){
		$select = 'SELECT modulo.*, submenu.id as id_submenu, submenu.nome as submenu_nome, submenu.nome_exibicao as submenu_nome_exibicao, submenu.icone as submenu_icone'
	    	. ' FROM modulo modulo'
    		. ' LEFT JOIN submenu submenu ON submenu.id = modulo.id_submenu AND submenu.ativo = 1'
	    	. ' WHERE modulo.ativo = 1';

	    return $this->select($select);
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