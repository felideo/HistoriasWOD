<?php
namespace Model;

use Libs;

class Instalacao {
	private $db;
	private $dados;
	private $usuario;

	public function criar_banco($dados, $usuario){
		$this->db      = new \PDO("mysql:host=" . $dados['host'], $dados['user'], $dados['password']);
		$this->dados   = $dados;
		$this->usuario = $usuario;

	    try {
	        $this->db->exec('DROP database ' . $dados['database']);

	        $this->create_database();
	        $this->create_tables();
	        $this->criar_usuario();
	    } catch (\Fail $e) {
			$e->show_error(true);
	    }

	    return ['sucesso' => true];
	}

	private function create_database(){
		$create = "CREATE DATABASE `" . $this->dados['database'] . "` CHARACTER SET utf8 COLLATE utf8_general_ci;";

		return [
	    	"sucesso" 	=> $this->db->exec($create),
	    	"erro" 		=> $this->db->errorCode(),
	    	"info" 		=> $this->db->errorInfo()
	    ];
	}

	private function create_tables(){
		$this->db->exec('set foreign_key_checks = 0;');
        $this->db->exec('USE ' . $this->dados['database']);

		$arquivo = fopen($_SERVER['DOCUMENT_ROOT'] . '/SQL.sql', "r");

		if($arquivo){
		    while (($linha = fgets($arquivo)) !== false) {
		        $retorno[] = [
			    	"sucesso" 	=> $this->db->exec($linha),
			    	"erro" 		=> $this->db->errorCode(),
			    	"info" 		=> $this->db->errorInfo()
			    ];
		    }

		    fclose($arquivo);
		}

		$this->db->exec('set foreign_key_checks = 1;');

		return $retorno;
	}

	private function criar_usuario(){
		$this->db->exec('set foreign_key_checks = 0;');
        $this->db->exec("INSERT INTO `usuario` VALUES (1,{$this->usuario['usuario']},{$this->usuario['senha']},1,1,1,0,1)");
        $this->db->exec("INSERT INTO `pessoa` VALUES (1,1,NULL,'','',NULL,0,1,1);");
		$this->db->exec('set foreign_key_checks = 1;');
	}
}


