<?php
namespace Controller;

class Acesso extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'acesso',
	];

	public function index($parametros){
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/front/login');
	}

	public function entrar(){
		$this->run_front();
	}

	public function run_front(){
		if($this->model->run_front(carregar_variavel('acesso'))){
			header('location: /board');
			exit;
		}

		$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
		header('location: /acesso');
		exit;
	}

	public function cadastrar(){
		$acesso = carregar_variavel('acesso');
		$bkp_acesso = $acesso;

		$usuario = $this->model->query
			->select('usuario.id, usuario.ativo')
			->from('usuario usuario')
			->where("usuario.email = '{$acesso['email']}' AND usuario.ativo = 1")
			->fetchArray()[0];

		if(!empty($usuario) && !empty([$usuario['ativo']])){
			$this->view->alert_js('usuario ja cadastrado no sistema...', 'erro');
			header('location: /acesso');
			exit;
		}

		$acesso['usuario']['hierarquia'] = 1;
		$acesso = $this->universe->get_controller('usuario')->insert_update($acesso);

		if(!isset($acesso['status']) || empty($acesso['status'])){
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro...', 'erro');
			header('location: /acesso');
			exit;
		}

		if(!isset($board['id']) || empty($board['id'])){
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro...', 'erro');
			header('location: /acesso');
			exit;
		}

		$bkp_acesso['email'] = $bkp_acesso['usuario']['email'];
		$bkp_acesso['senha'] = $bkp_acesso['usuario']['senha'];

		if($this->model->run_front($bkp_acesso)){
			header('location: /board');
			exit;
		}

		$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
		header('location: /acesso');
		exit;

		$this->run_front($acesso);
	}

	public function admin($parametros){
		if($this->universe->auth->is_logged(false)){
			header('location: /painel_controle/listagem');
			exit;
		}

		http_response_code(404);
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/back/login');
	}

	public function run_back(){
		if($this->model->run_back(carregar_variavel('acesso'))){
			header('location: /painel_controle/listagem');
			exit;
		}

		$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
		header('location: ' . \Libs\Redirect::getUrl());
		exit;
	}
}

