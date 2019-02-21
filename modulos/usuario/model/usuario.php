<?php
namespace Model;

use Libs;
use \Libs\QueryBuilder\QueryBuilder;

class Usuario extends \Framework\Model{
	public function carregar_listagem($busca, $datatable = null){
		$this->query->select('
				usuario.id,
				usuario.email,
				usuario.hierarquia,
				usuario.bloqueado,
				pessoa.nome,
				pessoa.sobrenome
			')
			->from('usuario usuario')
			->leftJoin('pessoa pessoa ON pessoa.id_usuario = usuario.id AND pessoa.ativo = 1')
			->where('usuario.ativo = 1 AND usuario.oculto = 0');

		if(isset($busca['search']['value']) && !empty($busca['search']['value'])){
			$where = "usuario.id LIKE '%{$busca['search']['value']}%'"
				. " OR usuario.email LIKE '%{$busca['search']['value']}%'"
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
					$this->query->orderBy("usuario.id {$busca['order'][0]['dir']}");
					break;

				case '1':
					$this->query->orderBy("CONCAT(pessoa.nome, ' ', pessoa.sobrenome) {$busca['order'][0]['dir']}");
					break;

				case '2':
					$this->query->orderBy("usuario.email {$busca['order'][0]['dir']}");
					break;

				default:
					$this->query->orderBy("usuario.id {$busca['order'][0]['dir']}");
					break;
			}
		}

		return $this->query->fetchArray();
	}

	public function load_user_by_email($email){
		try {
			$select = "SELECT * FROM usuario WHERE email = '{$email}' AND ativo = 1";

			return $this->select($select);
		}catch(Exception $e){
            if (ERROS) throw new Exception('<pre>' . $e->getMessage() . '</pre>');
		}
	}

	public function check_token($token){
		try {
			$select = "SELECT * FROM usuario WHERE token = '{$token}'";
			return $this->select($select);
		}catch(Exception $e){
            if (ERROS) throw new Exception('<pre>' . $e->getMessage() . '</pre>');
		}
	}

	public function load_cadastro($id){
		return $this->query->select('
				usuario.*,
				pessoa.*,
			')
			->from('usuario usuario')
			->leftJoin('pessoa pessoa ON pessoa.id_usuario = usuario.id AND pessoa.ativo = 1')
			->where("usuario.ativo = 1 AND usuario.id = {$id}")
			->fetchArray();
	}

	public function carregar_usuario_por_id($id){
		return $this->query->select('usuario.*, pessoa.*')
			->from('usuario usuario')
			->leftJoin('pessoa pessoa ON pessoa.id_usuario = usuario.id AND pessoa.ativo = 1')
			->where("usuario.ativo = 1 AND usuario.id = {$id}")
			->fetchArray();
	}
}