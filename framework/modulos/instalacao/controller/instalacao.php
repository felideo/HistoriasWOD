<?php
namespace Controller;

class Instalacao extends \Framework\Controller {
	protected $modulo = [
		'modulo' 	=> 'instalacao',
		'name'		=> 'Instalação',
		'send'		=> 'Instalar'
	];

	public function index() {
		$this->view->assign('modulo', $this->modulo);
		$this->view->render('back/cabecalho_rodape', $this->modulo['modulo'] . '/view/back/instalacao');
	}

	public function install() {
		$instalacao = carregar_variavel('instalacao');

		$this->create_config_file($instalacao);

		$retorno  = $this->model->criar_banco($instalacao['database'], $instalacao['usuario']);

		if(!empty($retorno['sucesso']) && $retorno['sucesso'] == true){
			$this->view->alert_js('Aplicação instalada com sucesso!!!', 'sucesso');
			header('location: /acesso/admin');
			exit;
		}

		$this->view->alert_js('Ocorreu um erro ao efetuar a instalação da aplicação, por favor tente novamente...', 'erro');
		header('location: /instalacao');
		exit;
	}

	private function create_config_file($instalacao){
		$config_file = "<?php\n"
			. "// Configuração do Fuso Horário\n"
			. "date_default_timezone_set('America/Sao_Paulo');\n\n"

			. '$protocolo = !empty($_SERVER["HTTPS"]) ? "https://" : "http://";' . "\n"
			. '$url       = $protocolo . $_SERVER["HTTP_HOST"] . "/";' . "\n\n"

			. "// Sempre use barra (/) no final das URLs\n"
			. 'define("URL", $url);' . "\n\n"

			. "// Configuração com Banco de Dados\n"
			. "define('DB_TYPE', 'mysql');\n"
			. "define('DB_HOST', '{$instalacao['database']['host']}');\n"
			. "define('DB_NAME', '{$instalacao['database']['database']}');\n"
			. "define('DB_USER', '{$instalacao['database']['user']}');\n"
			. "define('DB_PASS', '{$instalacao['database']['password']}');\n\n"

			. "define('DEVELOPER', true);\n"
			. "define('PREVENT_CACHE', true);\n\n"

			. "define('APP_NAME', '{$instalacao['informacoes']['nome']}');\n\n"

			. "if(function_exists('xdebug_disable')){\n"
			. "	\txdebug_disable();\n"
			. "}\n\n"

			. "define('EMAIL_EMAIL', '');\n"
			. "define('EMAIL_SENHA', '');\n";


			if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.php')){
				unlink($_SERVER['DOCUMENT_ROOT'] . '/config.php');
			}

		$arquivo = fopen($_SERVER['DOCUMENT_ROOT'] . '/config.php', 'w');
		fwrite($arquivo, $config_file);
		fclose($arquivo);
	}
}