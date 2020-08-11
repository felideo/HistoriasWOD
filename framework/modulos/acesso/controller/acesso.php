<?php
namespace ControllerCore;

class Acesso extends \Framework\Controller{

	protected $modulo = [
		'modulo' 	=> 'acesso',
	];

	public function index($parametros){
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/front/login');
	}

	public function entrar(){
		$acesso = carregar_variavel('acesso');
		$acesso['usuario']['email'] = $this->tratar_email($acesso['email']);
		$this->validar_email($acesso['email'], '/acesso');
		$this->run_front($acesso);
	}

	public function logout() {
		$this->universe->session->destroy();
		$this->redirect('/');
	}

	private function run_front($acesso){
		$acesso['usuario']['email'] = $this->tratar_email($acesso['email']);
		$this->validar_email($acesso['email'], '/acesso');

		if($this->model->run_front($acesso)){
			$this->view->alert_js('Seja bem vindo!', 'sucesso');
			$this->redirect('/');
		}

		$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
		$this->redirect('/acesso');
	}

	public function cadastrar(){
		$acesso = carregar_variavel('acesso');
		$acesso['usuario']['email'] = $this->tratar_email($acesso['usuario']['email']);
		$this->validar_email($acesso['usuario']['email'], '/acesso');
		$bkp_acesso = $acesso;

		$usuario = $this->model->query
			->select('usuario.id, usuario.ativo')
			->from('usuario usuario')
			->where("usuario.email = '{$acesso['usuario']['email']}' AND usuario.ativo = 1")
			->fetchArray()[0];

		if(!empty($usuario) && !empty([$usuario['ativo']])){
			$this->view->alert_js('usuario ja cadastrado no sistema...', 'erro');
			$this->redirect('/acesso');
		}

		$hierarquia_usuario_front = $this->model->query
			->select('config.id_hierarquia_usuario_frontend')
			->from('configuracao config')
			->where("id = 1")
			->fetchArray()[0];

		$acesso['usuario']['hierarquia'] = $hierarquia_usuario_front['id_hierarquia_usuario_frontend'];
		$acesso = $this->universe->get_controller('usuario')->insert_update($acesso);

		if(!isset($acesso['status']) || empty($acesso['status'])){
			$this->view->alert_js('Ocorreu um erro ao efetuar o cadastro...', 'erro');
			$this->redirect('/acesso');
		}

		$bkp_acesso['email'] = $bkp_acesso['usuario']['email'];
		$bkp_acesso['senha'] = $bkp_acesso['usuario']['senha'];

		$this->run_front($bkp_acesso);
	}

	public function admin($parametros){
		if($this->universe->auth->is_logged(false)){
			$this->redirect('/painel_controle/listagem');
		}

		http_response_code(404);
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/back/login');
	}

	public function run_back(){
		$acesso          = carregar_variavel('acesso');
		$acesso['email'] = $this->tratar_email($acesso['email']);
		$this->validar_email($acesso['email'], '/acesso');

		if($this->model->run_back($acesso)){
			$this->redirect("/painel_controle/listagem");
		}

		$this->view->alert_js('Usúario ou Senha inválido...', 'erro');
		$this->redirect(\Libs\Redirect::getUrl());
	}

	private function tratar_email($email){
		return trim(str_replace(' ', '', str_replace('"', '', str_replace("'", '', $email))));
	}

	private function validar_email($email, $redirect){
		$validacao = filter_var($email, FILTER_VALIDATE_EMAIL);

		if(!empty($redirect) && empty($validacao)){
			$this->view->alert_js('Email inválido...', 'erro');
			$this->redirect($redirect);
		}

		return $validacao;
	}
}

