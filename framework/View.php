<?php
namespace Framework;

class View {
	private $dwoo;
	private $model;
	private $assign;
	private $universe;

	public function __construct(){
		$this->model = new \Framework\GenericModel();
	}

	public function set_universe($universe){
		$this->universe = $universe;
		return $this;
	}

	public function shine(){
		$this->dwoo   = new \Dwoo\Core();
		$this->dwoo->setCompileDir('template_compile');
		$this->assign = new \Dwoo\Data();

		$this->assign('app_name', @defined(APP_NAME) ? APP_NAME : '');
		return $this;
	}

	public function assign($index, $data){
		$this->assign->assign($index, $data);
	}

	public function assign_array($data){
		foreach($data as $indice => $item){
			$this->assign->assign($indice, $item);
		}
	}

	public function getAssign($data){
		return $this->assign->get($data);
	}

	public function render($header_footer, $body){
		$this->assign('core_modulo', $this->universe->get_core_module());
		$this->set_todo();

		if(strpos($header_footer, 'sidebar')){
			$this->mount_sidebar();
		}

		if(!empty($header_footer)){
			$header = new \Dwoo\Template\File('views/' 		. rtrim(strtolower($header_footer), '/') 	. '/header.html');
		}

		$body = new \Dwoo\Template\File($this->definir_arquivo_body($body));

		if(!empty($header_footer)){
			$footer = new \Dwoo\Template\File('views/' 		. rtrim(strtolower($header_footer), '/') 	. '/footer.html');
		}

		if(isset($this->lazy_view) && !empty($this->lazy_view)){
			$this->assign('lazy_view', true);
		}

		$this->assign('_SESSION', $_SESSION);

		if(!empty($header_footer)){
			echo $this->dwoo->get($header, $this->assign);
		}

		echo $this->dwoo->get($body, $this->assign);

		if(!empty($header_footer)){
			echo $this->dwoo->get($footer, $this->assign);
		}

		if(isset($this->lazy_view) && !empty($this->lazy_view)){
			$lazy_view = new \Dwoo\Template\File('views/back/form_padrao/lazy_view.html');
			echo $this->dwoo->get($lazy_view, $this->assign);
		}

		$this->clear_template_compile_folder();
		exit;
	}

	private function definir_arquivo_body($body){
		if(file_exists($this->universe->get_core_module() . '/' . strtolower($body) . '.html')){
			return $this->universe->get_core_module() . '/' . strtolower($body) . '.html';
		}

		$caminho_cruzado = [
			'framework/modulos' => 'modulos',
			'modulos'           => 'framework/modulos'
		];

		if(file_exists($caminho_cruzado[$this->universe->get_core_module()] . '/' . strtolower($body) . '.html')){
			$this->assign('core_modulo', $caminho_cruzado[$this->universe->get_core_module()]);
			return $caminho_cruzado[$this->universe->get_core_module()] . '/' . strtolower($body) . '.html';
		}

		throw new \Fail('View Inexistente $core_modulo/' . $body);
	}

	private function clear_template_compile_folder(){
		$folder = 'template_compile';
		$files = glob($folder . '/*');
		foreach($files as $file){
		    if(is_file($file)){
		        unlink($file);
		    }
		}
	}

	public function render_include($include){
		$include = $this->carregar_include([$include]);

		if(!isset($include[0])){
			return false;
		}

		$include = $include[0];

		if(!file_exists('views/plataforma/' . $include['plataforma'][0]['identificador'] . '.html')){
			file_put_contents('views/plataforma/' . $include['plataforma'][0]['identificador'] . '.html', $include['html']);
		}

		$this->assign(strtoupper($include['plataforma'][0]['identificador']), 'views/plataforma/' . $include['plataforma'][0]['identificador'] . '.html');
		return 'views/plataforma/' . $include['plataforma'][0]['identificador'] . '.html';
	}


	public function processar_includes($includes){
		$includes = $this->carregar_include($includes);

		foreach($includes as $indice => $include){
			if(!file_exists('views/plataforma/' . $include['plataforma'][0]['identificador'] . '.html')){
				file_put_contents('views/plataforma/' . $include['plataforma'][0]['identificador'] . '.html', $include['html']);
				continue;
			}

			unset($includes[$indice]);
			$this->assign(strtoupper($include['plataforma'][0]['identificador']), 'views/plataforma/' . $include['plataforma'][0]['identificador'] . '.html');
		}

		// $this->assign_array($includes);
	}

	private function carregar_include($includes){
		$includes = implode(',', $includes);
		$includes = str_replace(',', "','", $includes);
		$includes = str_replace(' ', '', $includes);
		$includes = "'{$includes}'";

		$this->model->query->select('
				pagina.html,
				plataforma.identificador
			')
			->from('plataforma_pagina pagina')
			->leftJoin('plataforma plataforma ON plataforma.id = pagina.id_plataforma')
			->where("pagina.id_plataforma IN (SELECT id FROM plataforma WHERE identificador IN ({$includes}) AND ativo = 1 AND tipo = 'include')")
			->andWhere('pagina.ativo = 1')
			->orderBy('pagina.ultima_atualizacao DESC');

		if(!isset($_SESSION['plataforma']['modo_desenvolvedor']) || empty($_SESSION['plataforma']['modo_desenvolvedor'])){
			$this->model->query->andWhere('pagina.publicado = 1');
		}

		return $this->model->query
			->fetchArray();
	}

	private function render_include_arquivo($identificador){
		$pagina = new \Dwoo\Template\File('views/plataforma/' . $identificador . '.html');
		return $this->dwoo->get($pagina, $this->assign);
	}


	public function render_plataforma($header, $footer, $identificador, $includes = []){
		if(isset($this->lazy_view) && !empty($this->lazy_view)){
			$this->assign('lazy_view', true);
		}

		$this->assign('_SESSION', $_SESSION);

		// if(!empty($includes)){
		// 	$this->processar_includes($includes);
		// }

		if(file_exists('views/plataforma/' . $identificador . '.html') && (!isset($_SESSION['plataforma']['modo_desenvolvedor']) || empty($_SESSION['plataforma']['modo_desenvolvedor']))){
			$this->render_plataforma_arquivo($identificador);
		}

		$header = !empty($header) ? $this->carregar_pagina_plataforma($header) : '';
		$body   = $this->carregar_pagina_plataforma($identificador);
		$footer = !empty($footer) ? $this->carregar_pagina_plataforma($footer) : '';

		$pagina = $header . "\n\n" . $body . "\n\n" . $footer;

		if(!is_dir('views/plataforma')){
			mkdir('views/plataforma');
		}

		file_put_contents('views/plataforma/' . $identificador . '.html', $pagina);

		$this->render_plataforma_arquivo($identificador);
	}

	public function render_plataforma_arquivo($identificador){
		$pagina = new \Dwoo\Template\File('views/plataforma/' . $identificador . '.html');

		echo $this->dwoo->get($pagina, $this->assign);

		if(isset($this->lazy_view) && !empty($this->lazy_view)){
			$lazy_view = new \Dwoo\Template\File('views/back/form_padrao/lazy_view.html');
			echo $this->dwoo->get($lazy_view, $this->assign);
		}

		exit;
	}

	private function carregar_pagina_plataforma($identificador){
		$this->model->query->select('pagina.html')
			->from('plataforma_pagina pagina')
			->where("pagina.id_plataforma = (SELECT id FROM plataforma WHERE identificador = '{$identificador}'  AND ativo = 1)")
			->andWhere('pagina.ativo = 1')
			->orderBy('pagina.ultima_atualizacao DESC');

		if(!isset($_SESSION['plataforma']['modo_desenvolvedor']) || empty($_SESSION['plataforma']['modo_desenvolvedor'])){
			$this->model->query->andWhere('pagina.publicado = 1');
		}

		return $this->model->query->limit(1)
			->fetchArray()[0]['html'];
	}



	private function set_todo(){
		if((!defined('DEVELOPER') || empty(DEVELOPER)) || (!isset($GLOBALS['todo']) || empty($GLOBALS['todo']))){
			return false;
		}

		foreach($GLOBALS['todo'] as $indice => $todo){
			$this->warn_js($todo, 'info');
		}
	}

	private function mount_sidebar(){
		$array_menu = [];
		$submenus_com_permissao = [];

		$active = ($_SESSION['modulo_ativo'] == 'painel_controle') ? "active" : " ";

		$array_menu[] = "<li class='{$active}'>\n\t"
			. "<a href='/painel_controle/listagem'>\n\t\t"
			. "<span aria-hidden='true' class='fa fa-dashboard fa-fw'></span>\n\t\t"
            . "<span class='nav-label'>Painel de Controle</span>\n\t"
			. "</a>\n"
			. "</li>\n";

		foreach ($_SESSION['menus'] as $indice_01 => $menu){
			if(count($menu) == 1){
				if($_SESSION['usuario']['super_admin'] == 1 || isset($_SESSION['permissoes'][$menu[0]['modulo']])){

						$active = $menu[0]['modulo'] == $_SESSION['modulo_ativo'] ? "active" : " ";

						$string_menu = "<li class=' {$active} '>\n\t"
							. " <a href='/{$menu[0]['modulo']}/listagem'>\n\t\t"
							. 		"<span aria-hidden='true' class='icon fa {$menu[0]['icone']} fa-fw'></span>\n\t\t";

							$menu_submenu_nome = isset($menu[0]['submenu']) && !empty($menu[0]['submenu']) ? $menu[0]['submenu'] : $menu[0]['nome'];

                         	$string_menu .= "<span class='nav-label'>{$menu_submenu_nome}</span>\n\t"
	            			. "</a>\n"
	            			. "</li>\n";
	            }
           	}elseif(count($menu) > 1){
				foreach ($menu['modulos'] as $indice_02 => $submenu){
					if($_SESSION['usuario']['super_admin'] == 1 || isset($_SESSION['permissoes'][$submenu['modulo']])){
						$submenus_com_permissao[] = $indice_01;

					}
				}
			}


			if(isset($string_menu)){
				$array_menu = $this->insert_in_array_menu($array_menu, $menu, $string_menu);
			}

			if(isset($string_menu)){
				unset($string_menu);
			}
		}

		if(!empty($submenus_com_permissao)){
			$submenus_com_permissao = array_unique($submenus_com_permissao);


			foreach ($submenus_com_permissao as $indice_03 => $submenus){
				$ativos = [];

				foreach($_SESSION['menus'][$submenus]['modulos'] as $indice_04 => $submenu){
					$ativos[] = $submenu['modulo'];
				}

				$active = in_array($_SESSION['modulo_ativo'], $ativos) ? "active" : " ";
				$menu_submenu = "<li  class=' sub-sub-menu {$active} '>\n\t"
         			. " <a href='javascript:void(0)'>\n\t\t"
					. 		"<span aria-hidden='true' class='icon fa glyphicon {$_SESSION['menus'][$submenus]['icone']} fa-fw'></span>\n\t\t"
                    . 		"<span class='nav-label'>{$_SESSION['menus'][$submenus]['nome_exibicao']}</span>\n\t\t"
                    .		"<span class='fa arrow'></span>\n\t"
         			. " </a>\n\t"
     				. " <ul class='sub-menu'>\n\t\t";

     				$modulo_menor_ordem_submenu = 99999999999;

					foreach($_SESSION['menus'][$submenus]['modulos'] as $indice_04 => $submenu){
						if($_SESSION['usuario']['super_admin'] == 1 || isset($_SESSION['permissoes'][$submenu['modulo']])){
							$active = $submenu['modulo'] == $_SESSION['modulo_ativo'] ? "active" : " ";

 	                        $menu_submenu .= "<li class=' {$active} '>\n\t\t\t"
                         		. 	" <a href='/{$submenu['modulo']}/listagem'>\n\t\t\t\t"
								. 		"<span aria-hidden='true' class='icon fa glyphicon {$submenu['icone']} fa-fw'></span>\n\t\t\t\t"
 	                            . 		"<span class='nav-label'>{$submenu['nome']}</span>\n\t\t\t"
 	                            . 	" </a>\n\t\t"
 	                        	. "</li>\n\t";

 	                        	$modulo_menor_ordem_submenu = $submenu['ordem'] < $modulo_menor_ordem_submenu ? $submenu['ordem'] : $modulo_menor_ordem_submenu;
						}
					}

                 	$menu_submenu .= "</ul>\n"
         				. "</li>";

				$menu = [
					0 => [
						'ordem' => $modulo_menor_ordem_submenu,
						'modulo' => $submenus
					]
				];


				$array_menu = $this->insert_in_array_menu($array_menu, $menu, $menu_submenu);

        		unset($menu_submenu);
			}
		}

		ksort($array_menu);

		$retorno = [];

		foreach($array_menu as $indice => $menu){
			if(isset($menu['modulo']) && !empty($menu['modulo'])){
				$retorno[$menu['modulo']] = $menu['string_menu'];
				continue;
			}

			$retorno[$indice] = $menu;
		}

		$array_menu = implode(' ', $retorno);

		$this->assign('sidebar_painel_administrativo', $array_menu);
	}

	private function insert_in_array_menu($array_menu, $menu, $string_menu){
		if(!isset($array_menu[$menu[0]['ordem']])){
			$array_menu[$menu[0]['ordem']] = [
				'string_menu' => $string_menu,
				'modulo'      => $menu[0]['modulo'],
			];

			return $array_menu;
		}

		$menu[0]['ordem']++;

		return $this->insert_in_array_menu($array_menu, $menu, $string_menu);
	}

	public function set_colunas_datatable($colunas){

		foreach ($colunas as $indice => $coluna) {
			if($indice == 0){
				$retorno_coluna[] = "<th aria-sort='ascending' colspan='1' rowspan='1' tabindex='0' class='sorting_asc'>{$coluna}</th>";
			} else {
				$retorno_coluna[] = "<th colspan='1' rowspan='1' tabindex='0' class='sorting'>{$coluna}</th>";

			}
		}

		$this->assign('colunas_datatable', $retorno_coluna);
	}


	public function warn_js($mensagem, $status){
		switch ($status) {
			case 'atencao':
				$status = 'warn';
				$icone = '<i class="fa fa-warning"></i>';
				break;

			case 'erro':
				$status = 'error';
				$icone = '<i class="fa fa-frown-o"></i>';
				break;

			case 'sucesso':
				$status = 'success';
				$icone = '<i class="fa fa-check"></i>';
				break;

			case 'info':
				$status = 'info';
				$icone = '<i class="fa fa-info-circle"></i>';
				break;

			case 'base':
				$status = 'base';
				$icone = '<i class="fa fa-send"></i>';
				break;
		}

		$mensagem = $icone . ' ' . $mensagem;

		$_SESSION['notificacoes'][] = ""
			. " setTimeout(function(){\n\t"
			. " 	$.notify('{$mensagem}', {\n\t\t"
	        . " 	    style: 'appkit',\n\t\t"
	        . " 	    className: '$status',\n\t\t"
	        . " 	    hideAnimation: 'fadeOut',\n\t\t"
	        . " 	    showAnimation: 'fadeIn',\n\t\t"
	        . " 		autoHideDelay: 10000,\n\t"
	        . " 	})\n"
	        . " }, 1000);\n\n";
	}

	public function alert_js($mensagem, $status){
		switch ($status) {
			case 'atencao':
				$status = 'warning';
				$title = "Atenção!";
				break;

			case 'erro':
				$status = 'error';
				$title = "Erro!";
				break;

			case 'sucesso':
				$status = 'success';
				$title = "Sucesso!";
				break;

			case 'info':
				$status = 'info';
				$title = "Info!";
				break;
		}

		$_SESSION['alertas'] = ""
			. " 	swal({\n"
			. "			title: '{$title}',\n"
			. "  		\ttext: '{$mensagem}',\n"
			. "  		\ttype: '{$status}',\n"
			. "  		\tconfirmButtonText: 'OK'\n"
			. "		},\n"
			. " 	\tfunction(){\n"
			. "			console.log('lerolero');\n"
			. " 		\t$.ajax({\n"
			. "			\turl: '/master/limpar_alertas_ajax',\n"
			. " 		\tsuccess: function(retorno){\n"
			. "				\tconsole.log(retorno);\n"
			. "   		\t}\n"
			. "		\t})\n"
			. "		});";
	}

	public function lazy_view(){

		$this->lazy_view = true;
	}



	public function default_buttons_listagem($id, $visualizar = true, $editar = true, $excluir = true){
		$protocolo = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$url       = $protocolo . $_SERVER['HTTP_HOST'] . '/';

		$botao_visualizar = '';
		$botao_editar     = '';
		$botao_excluir    = '';

		if($visualizar){
			$botao_visualizar = $this->universe->permission->check_user_permission($this->modulo['modulo'], "visualizar") ?
				"<a href='/{$this->modulo['modulo']}/visualizar/{$id}' title='Visualizar'><i class='botao_listagem fa fa-eye fa-fw'></i></a>" :
				'';
			}

		if($editar){
			$botao_editar = $this->universe->permission->check_user_permission($this->modulo['modulo'], "editar") ?
				"<a href='/{$this->modulo['modulo']}/editar/{$id}' title='Editar'><i class='botao_listagem fa fa-pencil fa-fw'></i></a>" :
				 '';
		}

		$delete_message = 'Tem certeza que deseja deletar o registro?';

		if(isset($this->modulo['delete_message']) && !empty($this->modulo['delete_message'])){
			$delete_message = $this->modulo['delete_message'];
		}

		if($excluir){
			$botao_excluir = $this->universe->permission->check_user_permission($this->modulo['modulo'], "deletar") ?
				"<a class='validar_deletar' href='javascript:void(0)' data-id_registro='{$id}' data-mensagem='{$delete_message}' data-redirecionamento='{$url}{$this->modulo['modulo']}/destroy/{$id}' title='Deletar'><i class='botao_listagem  fa fa-trash-o fa-fw'></i></a>" :
				'';
		}

		return $botao_visualizar . $botao_editar . $botao_excluir;
	}
}