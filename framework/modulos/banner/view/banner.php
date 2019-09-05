<?php
namespace Controller;

use Libs;

class Banner extends \Framework\ControllerCrud {

	protected $modulo = [
		'modulo' 	=> 'banner'
	];

	protected $datatable = [
		'colunas' => ['ID <i class="fa fa-search"></i>', 'Curso <i class="fa fa-search"></i>', 'Ações'],
		'select'  => ['id', 'curso'],
		'from'    => 'curso',
		'search'  => ['id', 'curso']
	];

	protected function carregar_dados_listagem_ajax($busca){
		$query = $this->model->carregar_listagem($busca, $this->datatable);

		$retorno = [];

		foreach ($query as $indice => $item) {
			$retorno[] = [
				$item['id'],
				$item['curso'],
				$this->view->default_buttons_listagem($item['id'], true, true, true)
			];
		}

		return $retorno;
	}

	public function delete($id) {
		$this->universe->auth->is_logged(true);
		$this->universe->permission->check($this->modulo['modulo'], "deletar");

		$this->check_if_exists($id[0]);

		$curso_utilizado = $this->model->query->select('
				trabalho.id
			')
			->from('trabalho trabalho')
			->where("trabalho.id_curso = {$id[0]}")
			->fetchArray();

		if(!empty($curso_utilizado)){
			$msg = 'O curso esta relacionado ao(s) trabalho(s) ID numero: ';

			foreach($curso_utilizado as $indice => $trabalho){
				$msg .= $trabalho['id'] . ', ';
			}

			$msg = rtrim($msg, ', ');
			$msg .= '. Exclusão negada! Remova manualmente este curso de todos os trabalhos antes de tentar excluir-lo.';

			$this->view->alert_js($msg, 'erro');
			header('location: /' . $this->modulo['modulo']);
			exit;
		}

		$retorno = $this->model->delete($this->modulo['modulo'], ['id' => $id[0]]);

		if($retorno['status']){
			$this->view->alert_js(ucfirst($this->modulo['modulo']) . ' removido com sucesso!!!', 'sucesso');
		} else {
			$this->view->alert_js('Ocorreu um erro ao efetuar a remoção do ' . strtolower($this->modulo['modulo']) . ', por favor tente novamente...', 'erro');
		}

		header('location: /' . $this->modulo['modulo']);
		exit;
	}

	public function buscar_curso_select2(){
		$busca = carregar_variavel('busca');

		$retorno = $this->model->buscar_curso($busca);

		if(isset($busca['cadastrar_busca']) && !empty($busca['cadastrar_busca']) && $busca['cadastrar_busca'] == 'true' && $busca['nome'] != '%%'){
			$add_cadastro[0] = [
				'id'               => $busca['nome'],
				'curso'             => "<strong>Cadastrar Novo curso: </strong>" . $busca['nome']
			];

			$retorno = array_merge($add_cadastro, $retorno);
		}

		echo json_encode($retorno);
		exit;
	}
}