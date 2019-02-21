<?php
namespace Model;

class Acesso extends \Framework\Model{
	private $menus;
	private $acesso;
	private $modulos;
	private $usuario;
	private $session;
	private $permissoes;

	public function run_back($acesso){
		$this->acesso = $acesso;

		$this->verificar_usuario_senha();

		if(empty($this->usuario)){
			return false;
		}

		$this->carregar_hierarquia();
		$this->montar_sessao();
		$this->load_permissions();
		$this->load_modulos_and_menus();

		\Libs\Session::set('logado', true);
		\Libs\Session::set('menus', $this->menus);
		\Libs\Session::set('usuario', $this->usuario);
		\Libs\Session::set('modulos', $this->modulos);
		\Libs\Session::set('permissoes', $this->permissoes);

		return true;
	}

	private function verificar_usuario_senha(){
		$this->usuario = $this->query
			->select('usuario.*')
			->from('usuario usuario')
			->where("usuario.email = '{$this->acesso['email']}'")
			->andWhere("usuario.senha = '{$this->acesso['senha']}'")
			->andWhere("usuario.ativo = 1")
			->andWhere("usuario.bloqueado = 0")
			->fetchArray()[0];
	}

	private function carregar_hierarquia(){
		$this->hierarquia = $this->query
			->select('hierarquia.*')
			->from('hierarquia hierarquia')
			->where("hierarquia.ativo = 1 AND hierarquia.id = {$this->usuario['hierarquia']}")
			->fetchArray()[0];
	}

	public function montar_sessao(){
		$this->sessao = [
				'id'               => $this->usuario['id'],
				'nome'             => $this->usuario['email'],
				'hierarquia'       => $this->usuario['hierarquia'],
				'hierarquia_nivel' => $this->hierarquia['nivel'],
				'super_admin'      => $this->usuario['super_admin']
			];
	}

	private function load_modulos_and_menus(){
		$this->carregar_ordem_preferencial_menu();

		$modulos = $this->select('SELECT * FROM modulo WHERE ATIVO = 1 ORDER BY ordem');
		$submenus = $this->select('SELECT * FROM submenu WHERE ATIVO = 1');

		foreach($modulos as $indice_01 => $modulo){
			if($modulo['hierarquia'] == 0 && empty($this->usuario['super_admin'])){
				continue;
			}

			$retorno_modulos[$modulo['modulo']] = $modulo;

			if(isset($this->ordem_preferenciao[$retorno_modulos[$modulo['modulo']]['id']]) && !empty($this->ordem_preferenciao[$retorno_modulos[$modulo['modulo']]['id']])){
				$retorno_modulos[$modulo['modulo']]['ordem'] = $this->ordem_preferenciao[$retorno_modulos[$modulo['modulo']]['id']]['ordem'];
			}
		}

		\Libs\Arrays::ordenarPorColuna($retorno_modulos, 'ordem', 'ASC');

		foreach($retorno_modulos as $key => $modulo){
			if(empty($modulo['id_submenu'])){
				$menus[$modulo['modulo']][0] = $modulo;
				continue;
			}

			foreach ($submenus as $indice_02 => $submenu) {

				$menus[$submenu['nome']]['icone'] = $submenu['icone'];
				$menus[$submenu['nome']]['nome_exibicao'] = $submenu['nome_exibicao'];

				if($modulo['id_submenu'] == $submenu['id']){
					$menus[$submenu['nome']]['modulos'][$modulo['modulo']] = $modulo;
				}
			}
		}

		$this->menus   = $menus;
		$this->modulos = $retorno_modulos;
	}

	private function carregar_ordem_preferencial_menu(){
		$ordem_preferenciao = $this->select("SELECT * FROM ordem_usuario_menu WHERE ATIVO = 1 AND id_usuario = {$this->usuario['id']} ORDER BY ordem");

		if(empty($ordem_preferenciao)){
			$this->ordem_preferenciao = null;
		}

		foreach($ordem_preferenciao as $indice => $item){
			$this->ordem_preferenciao[$item['id_modulo']] = $item;
		}
	}

	private function load_permissions(){
		$hierarquia = empty($this->usuario['hierarquia']) ? 'NULL' : $this->usuario['hierarquia'];

		$select = 'SELECT hierarquia.id as id_hierarquia, hierarquia.nome,'
			. ' relacao.id as id_relacao,'
			. ' permissao.id as id_permissao, permissao.permissao, permissao.id_modulo,'
			. ' modulo.modulo'
			. ' FROM hierarquia hierarquia'
			. ' LEFT JOIN hierarquia_relaciona_permissao relacao'
			. ' ON relacao.id_hierarquia = hierarquia.id AND relacao.ativo = 1'
			. ' LEFT JOIN permissao permissao'
			. ' ON permissao.id = relacao.id_permissao'
			. ' LEFT JOIN modulo modulo'
			. ' ON modulo.id = permissao.id_modulo'
			. ' WHERE hierarquia.id = ' . $hierarquia;

		$permissoes = $this->select($select);

		if(empty($permissoes)){
			$this->permissoes = null;
			return;
		}

		foreach($permissoes as $indice => $permissao){
			$retorno_permissoes[$permissao['modulo']][$permissao['permissao']] = $permissao;
		}


		$this->permissoes = $retorno_permissoes;
	}
}