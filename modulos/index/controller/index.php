<?php
namespace Controller;

class Index extends \Framework\Controller {

	protected $modulo = [
		'modulo'      => 'index',
		'name'        => 'Index',
		'table'       => 'index',
		'send'        => null,
		'localizador' => null,
		'seo'         => [
			'habilitado'    => false,
			'title_padrao'  => null,
			'robots_padrao' => null,
			'revise_padrao' => null,
		],
		'url'         => [
			'coluna'    => null,
			'metodo'    => null,
			'atualizar' => false
		]
	];

	public function index($parametros){
		$this->view->assign('ultimos_posts', $this->universe->get_model('post')->carregar_ultimos_posts());
		$this->view->assign('posts', $this->universe->get_model('post')->carregar_post());
		$this->view->assign('livros', $this->universe->get_model('livro')->carregar_livro());
		$this->view->render_plataforma('', '', 'index', ['site_cabecalho', 'site_rodape', 'post_item', 'sidebar', 'menu', 'seo']);
	}

	public function formatar_texto(){
		$texto   = '';
		$arquivo = fopen("teste.txt", "r");

		if($arquivo){
			while(($linha = fgets($arquivo)) !== false){
				if(!empty($linha) && strlen($linha) > 1){
					$linha = str_replace(["\r", "\n"], '', $linha);
					$texto .= " $linha";
				}else{
					$texto .= " \n\n";
				}
			}


			fclose($arquivo);
		}

		file_put_contents('formatado.txt', $texto);

		$texto   = '';
		$arquivo = fopen("formatado.txt", "r");

		if($arquivo){
			while(($linha = fgets($arquivo)) !== false){
				if(!empty($linha) && strlen($linha) > 1){
					$texto .= trim($linha);
				}else{
					$texto .= " \n\n";
				}
			}

			fclose($arquivo);
		}

		debug2($texto);

		unlink('formatado.txt');
		file_put_contents('formatado.txt', $texto);
		debug2('Fim \0/');
		exit;
	}
}