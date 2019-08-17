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
		// echo "Não está pronto ainda. Nada de cadastrar por em quanto =/";
		// exit;

		$acesso = carregar_variavel('acesso');

		debug2($acesso);
		$bkp_acesso = $acesso;

		// $usuario = $this->model->query
		// 	->select('usuario.id, usuario.ativo')
		// 	->from('usuario usuario')
		// 	->where("usuario.email = '{$acesso['email']}' AND usuario.ativo = 1")
		// 	->fetchArray()[0];

		// if(!empty($usuario) && !empty([$usuario['ativo']])){
		// 	$this->view->alert_js('usuario ja cadastrado no sistema...', 'erro');
		// 	header('location: /acesso');
		// 	exit;
		// }

		$acesso['usuario']['hierarquia'] = 1;
		$acesso = $this->get_controller('usuario')->insert_update($acesso);

		if(!isset($acesso['status']) || empty($acesso['status'])){
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro...', 'erro');
			header('location: /acesso');
			exit;
		}

		$model_board = $this->get_model('board');
		$board = $model_board->cadastrar_board($acesso['usuario']['retorno']['id']);

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
		if(\Libs\Auth::esta_logado()){
			header('location: /painel_controle');
			exit;
		}

		http_response_code (404);

		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/back/login');
	}

	public function run_back(){
		if($this->model->run_back(carregar_variavel('acesso'))){
			$this->model->run_front(carregar_variavel('acesso'));
			header('location: /painel_controle');
			exit;
		}

		$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
		header('location: ' . \Libs\Redirect::getUrl());
		exit;
	}
}

