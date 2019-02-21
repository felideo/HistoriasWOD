<?php
namespace Controller;

use Libs;

class Usuario extends \Framework\ControllerCrud {
	private $hierarquia_organizada = [];

	protected $modulo = [
		'modulo'         => 'usuario',
		'delete_message' => 'Tem certeza que deseja deletar este usuario?'
	];

	protected $datatable = [
		'colunas' => ['ID  <i class="fa fa-search"></i>', 'Nome  <i class="fa fa-search"></i>', 'Email  <i class="fa fa-search"></i>', 'Hierarquia', 'Ações'],
		'from'    => 'usuario',
		'ordenacao_desabilitada' => '3, 4'
	];


	public function middle_index() {

		$this->view->assign('hierarquia_list', $this->model->load_active_list('hierarquia'));
	}

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca);

		$retorno = [];

		foreach($this->model->load_active_list('hierarquia') as $indice => $hierarquia) {
			$this->hierarquia_organizada[$hierarquia['id']] = $hierarquia['nome'];
		}

		$url = URL;
		$permissao_remover_acesso = \Libs\Permission::check_user_permission($this->modulo['modulo'], 'remover_conceder_acesso');

		if(empty($query)){
			$query = [];
		}

		foreach ($query as $indice => $item) {

			$remover_acesso = '';

			if(!empty($permissao_remover_acesso)){
				if(empty($item['bloqueado']) && $_SESSION['usuario']['id'] != $item['id'] ? true : false){
					$remover_acesso = "<a class='validar_deletar' href='javascript:void(0)' data-id_registro='{$item['id']}'"
						 . " data-redirecionamento='{$url}/{$this->modulo['modulo']}/remover_conceder_acesso/{$item['id']}/1'"
						 . " data-mensagem='Tem certeza que deseja remover o acesso deste usuario?'"
						 . " title='Remover Acesso'><i class='botao_listagem fa fa-minus-circle fa-fw'></i></a>";
				}

				if(!empty($item['bloqueado']) && $_SESSION['usuario']['id'] != $item['id'] ? true : false){
					$remover_acesso = "<a class='validar_deletar' href='javascript:void(0)' data-id_registro='{$item['id']}'"
						 . " data-redirecionamento='{$url}/{$this->modulo['modulo']}/remover_conceder_acesso/{$item['id']}/0'"
						 . " data-mensagem='Tem certeza que deseja conceder acesso para este usuario?'"
						 . " title='Conceder Acesso'><i class='botao_listagem fa fa-check-circle fa-fw'></i></a>";
				}
			}

			$retorno[] = [
				$item['id'],
				(isset($item['pessoa'][0]) ? $item['pessoa'][0]['nome'] : '') . ' ' . (isset($item['pessoa'][0]) ? $item['pessoa'][0]['sobrenome'] : ''),
				$item['email'],
				!empty($item['hierarquia']) && isset($this->hierarquia_organizada[$item['hierarquia']]) ? $this->hierarquia_organizada[$item['hierarquia']] : '',
				$this->view->default_buttons_listagem($item['id'], true, $_SESSION['usuario']['id'] != $item['id'] ? true : false, $_SESSION['usuario']['id'] != $item['id'] ? true : false) . $remover_acesso
			];
		}

		return $retorno;
	}

	public function remover_conceder_acesso($parametros){
		\Libs\Auth::handLeLoggin();
		\Libs\Permission::check($this->modulo['modulo'], "remover_conceder_acesso");

		$this->check_if_exists($parametros[0]);

		$retorno = $this->model->insert_update(
			$this->modulo['modulo'],
			['id' => $parametros[0]],
			['bloqueado' => $parametros[1]],
			true
		);

		switch ($parametros[1]) {
			case '0':
				$msg_retorno_sucesso = 'Concedido';
				$msg_retorno_erro   = 'Concedido';
				break;

			case '1':
				$msg_retorno_sucesso = 'Removido';
				$msg_retorno_erro   = 'concessão';
				break;
		}

		if(isset($retorno['status']) && !empty($retorno['status'])){
			$this->view->alert_js('Acesso do usuario ' . $msg_retorno_sucesso . ' com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a ' . $msg_retorno_erro . ' do acesso do usuario, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;
	}

	public function insert_update($usuario, $where = null){
		if(empty($where['id'])){
			// $usuario['senha']         = \Libs\Hash::get_unic_hash()
			$usuario['usuario']['senha'] = 12345;
			$usuario['usuario']['ativo'] = 1;
			$where['email']              = $usuario['usuario']['email'];
		}

		// $usuario['usuario']['email'] = 'felideo+swdb@gmail.com';

		$usuario['usuario']['retorno'] = $this->model->insert_update(
			$this->modulo['modulo'],
			$where,
			$usuario['usuario'],
			true
		);

		if(!empty($usuario['usuario']['retorno']['status'])){
			$usuario['pessoa']['id_usuario'] = $usuario['usuario']['retorno']['id'];

			$usuario['pessoa']['retorno'] = $this->model->insert_update(
				'pessoa',
				['id_usuario' => $usuario['pessoa']['id_usuario']],
				$usuario['pessoa'],
				true
			);

			// $verificacao_usuario = $this->model->load_cadastro($usuario['usuario']['retorno']['id'])[0];


			// if(!empty($verificacao_usuario['ativo']) && empty($verificacao_usuario['bloqueado'])){
			// 	$email = new \Libs\Mail();

			// 	$msg = "Olá {$verificacao_usuario['pessoa'][0]['nome']} {$verificacao_usuario['pessoa'][0]['sobrenome']}<br><br>"
			// 		. " Voce foi cadastrado no sistema SWDB - http://swdb.felideo.com<br>"
			// 		. " Sua senha é: {$verificacao_usuario['senha']}<br><br>"
			// 		. " Para acessar use o link: http://swdb.felideo.com/acesso/admin";

			// 	$email->set_from(EMAIL_EMAIL)
			// 		->set_pass(EMAIL_SENHA)
			// 		->set_to(trim($verificacao_usuario['email']))
			// 		->set_assunto('Cadastro no SWDB')
			// 		->set_mensagem($msg)
			// 		->send_mail();
			// }
		}

		if(!empty($usuario['usuario']['retorno']['status'] && $usuario['pessoa']['retorno']['status'])){
			$usuario['status'] = $usuario['usuario']['retorno']['status'] && $usuario['pessoa']['retorno']['status'];
			return $usuario;
		}

		return $usuario;
	}

	public function middle_editar($id) {
		$cadastro = $this->model->load_cadastro($id)[0];
		$cadastro['hierarquia_nivel'] = $this->model->query
			->select('hierarquia.*')
			->from('hierarquia hierarquia')
			->where("hierarquia.ativo = 1 AND hierarquia.id = {$cadastro['hierarquia']}")
			->fetchArray()[0]['nivel'];

		$this->view->assign('cadastro', $cadastro);
		$this->view->assign('hierarquia_list', $this->model->load_active_list('hierarquia'));
	}

	public function middle_visualizar($id){
		$cadastro = $this->model->load_cadastro($id)[0];
		$cadastro['hierarquia_nivel'] = $this->model->query
			->select('hierarquia.*')
			->from('hierarquia hierarquia')
			->where("hierarquia.ativo = 1 AND hierarquia.id = {$cadastro['hierarquia']}")
			->fetchArray()[0]['nivel'];

		$this->view->assign('cadastro', $cadastro);
		$this->view->assign('hierarquia_list', $this->model->load_active_list('hierarquia'));
	}

	public function editar_meu_perfil($id){
		\Libs\Auth::handLeLoggin();

		if($_SESSION['usuario']['id'] != $id[0]){
			header('location: /usuario/editar_meu_perfil/' . $_SESSION['usuario']['id']);
			exit;
		}

		$this->modulo['name'] = 'Minha Conta';
		$this->view->assign('modulo', $this->modulo);

		$cadastro = $this->model->carregar_usuario_por_id($_SESSION['usuario']['id']);
		$this->view->assign('cadastro', $cadastro[0]);
		$this->view->render('back/cabecalho_rodape_sidebar', $this->modulo['modulo'] . '/view/perfil/editar_meu_perfil');
	}

	public function update_meus_dados($id){
		\Libs\Auth::handLeLoggin();

		if($_SESSION['usuario']['id'] != $id[0]){
			header('location: /usuario/editar_meu_perfil/' . $_SESSION['usuario']['id']);
			exit;
		}

		$cadastro = $this->model->carregar_usuario_por_id($_SESSION['usuario']['id'])[0];
		$update   = carregar_variavel('usuario');

		$update['pessoa'] = array_filter($update['pessoa'], function($item){
			if(empty($item)){
				unset($item);
			}

  			return isset($item) ? $item : null;
		});

		$update['usuario'] = array_filter($update['usuario'], function($item){
			if(empty($item)){
				unset($item);
			}

  			return isset($item) ? $item : null;
		});

		if(isset($update['usuario']['senha_antiga']) || isset($update['usuario']['senha'])){
			if(!isset($update['usuario']['senha_antiga'])){
				$this->view->warn_js('Obrigatorio indicar a senha antiga para efetuar a atualização de senha. Atualização de senha negada!', 'erro');
				unset($update['usuario']['senha_antiga']);
				unset($update['usuario']['senha']);
			}

			if(!isset($update['usuario']['senha'])){
				$this->view->warn_js('Obrigatorio indicar a nova senha para efetuar a atualização de senha. Atualização de senha negada!', 'erro');
				unset($update['usuario']['senha_antiga']);
				unset($update['usuario']['senha']);
			}

			if(isset($update['usuario']['senha_antiga']) && $update['usuario']['senha_antiga'] != $cadastro['senha']){
				$this->view->warn_js('Senha antiga incorreta. Atualização de senha negada!', 'erro');
				unset($update['usuario']['senha_antiga']);
				unset($update['usuario']['senha']);
			}
		}

		if(empty($update['usuario'])){
			$update['usuario']['ativo'] = 1;
		}

		if(!isset($update['pessoa']['nome']) || !isset($update['pessoa']['sobrenome']) || empty($update['pessoa']['nome']) || empty($update['pessoa']['sobrenome'])){
			$this->view->alert_js('Proibido que o usuario deixe o Nome ou Sobrenome em branco!', 'erro');
			header('location: /usuario/editar_meu_perfil/' . $id[0]);
			exit;
		}

		$retorno = $this->insert_update($update, ['id' => $id[0]]);

		if(isset($retorno['status']) && !empty($retorno['status'])){
			$this->view->alert_js('Os dados da sua conta foram atualizados com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a alteração dos dados da sua conta, por favor tente novamente...', 'erro');
		}

		header('location: /usuario/editar_meu_perfil/' . $id[0]);
		exit;
	}

	public function salvar_ordem_preferencial_menu_ajax(){
		$nova_ordem = carregar_variavel('data');

		foreach($nova_ordem as $indice => $item){
			$update = [
				'id_usuario' => $_SESSION['usuario']['id'],
				'id_modulo'  => $item['id_modulo'],
				'ordem'      => $item['ordem'],
				'ativo'      => 1
			];

			$retorno = $this->model->insert_update(
				'ordem_usuario_menu',
				['id_usuario' => $_SESSION['usuario']['id'], 'id_modulo' => $item['id_modulo']],
				$update,
				true
			);

			if(!empty($retorno)){
				debug2($_SESSION['menus']);
				debug2(isset($_SESSION['menus'][0][$item['modulo']]));
				debug2($item);
				if(!isset($_SESSION['menus'][$item['modulo']])){
					continue;
				}

				$_SESSION['menus'][$item['modulo']][0]['ordem'] = $item['ordem'];
				$_SESSION['modulos'][$item['modulo']]['ordem']  = $item['ordem'];
			}
		}

		echo json_encode(true);
		exit;
	}

















	private function listagem($dados_linha){
		debug2($dados_linha);
		exit;

		if(empty($dados_linha)){
			return false;
		}

		foreach ($this->model->load_active_list('hierarquia') as $indice => $hierarquia) {
			$hierarquias[$hierarquia['id']] = $hierarquia['nome'];
		};

		foreach ($dados_linha as $indice => $linha) {
			if($linha['super_admin'] != 1){

				$hierarquia_exibicao = isset($hierarquias[$linha['hierarquia']]) ? $hierarquias[$linha['hierarquia']] : 'Usuario Site' ;

			$botao_permitir_cadastro = $linha['hierarquia'] == 2 ?
				"<a href='/{$this->modulo['modulo']}/permitir_cadastro/{$linha['id']}' title='Permitir Cadastro'><i class='fa fa-thumbs-up fa-fw'></i></a>" :
				'';


			$botao_proibir_cadastro = $linha['hierarquia'] == 4 ?
				"<a href='/{$this->modulo['modulo']}/proibir_cadastro/{$linha['id']}' title='Proibir Cadastro'><i class='fa fa-thumbs-down fa-fw'></i></a>" :
				'';



				$retorno_linhas[] = [
					"<td class='sorting_1'>{$linha['id']}</td>",
	        		"<td>{$linha['email']}</td>",
	        		"<td>{$hierarquia_exibicao}</td>",
	        		"<td>" . $this->view->default_buttons_listagem($linha['id']) . $botao_permitir_cadastro . $botao_proibir_cadastro . "</td>"
				];
			}
		}

		if(!isset($retorno_linhas)){
			return false;
		}

		$this->view->linhas_datatable = $retorno_linhas;
	}


	public function verificar_duplicidade_ajax(){
		echo json_encode(empty($this->model->load_user_by_email(carregar_variavel('usuario'))));
		exit;
	}











	public function perfil(){
		$cadastro = $this->model->carregar_usuario_por_id($_SESSION['usuario']['id']);
		$this->view->cadastro = $cadastro[0];
		$this->view->render('front/cabecalho_rodape', 'front/usuario/perfil');
	}

	public function update_perfil($id){
		$usuario = carregar_variavel('usuario');
		debug2($usuario);

		if(isset($usuario['senha']) && !empty($usuario['senha'])){
			$update_db = [
				'senha' => $usuario['senha']
			];

			$retorno_usuario = $this->model->update('usuario', $update_db, ['id' => $id[0]]);
		}

		unset($update_db);

		$update_db = [
			'pronome' 	  => $usuario['pronome'],
    		'nome'        => $usuario['nome'],
    		'sobrenome'   => $usuario['sobrenome'],
    		'instituicao' => $usuario['instituicao'],
    		'atuacao'     => $usuario['atuacao'],
    		'lattes'      => $usuario['lattes'],
    		'grau'        => $usuario['grau'],
		];

		$retorno_pessoa = $this->model->update('pessoa', $update_db, ['id' => $id[0]]);

		if($retorno_usuario['status'] == 1 || $retorno_pessoa['status'] == 1){
			$this->view->alert_js('Edição efetuada com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro, por favor tente novamente...', 'erro');
		}

		header('location: /index');
		exit;

	}

	public function permitir_cadastro($id){
		if(empty($this->model->select("SELECT id FROM {$this->modulo['modulo']} WHERE id = {$id[0]} AND ativo = 1"))){
			$this->view->alert_js("{$this->modulo['send']} não existe...", 'erro');
			header('location: ' . URL . $this->modulo['modulo']);
			exit;
		}

		$update_db = [
			"hierarquia" => 4
		];

		$retorno = $this->model->update($this->modulo['modulo'], $update_db, ['id' => $id[0]]);

		if($retorno['status']){
			$this->view->alert_js('Alteração efetuada com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a alteração, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;

	}
	public function proibir_cadastro($id){
		if(empty($this->model->select("SELECT id FROM {$this->modulo['modulo']} WHERE id = {$id[0]} AND ativo = 1"))){
			$this->view->alert_js("{$this->modulo['send']} não existe...", 'erro');
			header('location: ' . URL . $this->modulo['modulo'] . '/');
			exit;
		}

		$update_db = [
			"hierarquia" => 2
		];

		$retorno = $this->model->update($this->modulo['modulo'], $update_db, ['id' => $id[0]]);

		if($retorno['status']){
			$this->view->alert_js('Alteração efetuada com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a alteração, por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;

	}
}