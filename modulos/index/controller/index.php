<?php
namespace Controller;

class Index extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'index',
		'name'		=> 'Index',
		'send'		=> 'Index'
	];

	private $acessos = [
		/* Felideo
		// 'app_id'          => 'cha-de-panela',
		// 'app_key'         => '015253F5C2C2063EE42BEF887D4337B5',
		// 'email'           => 'felideo@gmail.com',
		// 'token'           => '13cb5b42-bbdf-40ee-aadd-a412b6ec8570f6df1c0f4c8384edfb5a094c833074a46064-02d6-4653-a918-245dbb0175a6',
		*/

		'email' => 'daraefer@yahoo.com.br',
		'token' => '2818390d-ea40-4a3b-a9d1-d2ee8e5f347c84c18da64f42953a695764fa01777d8c8c67-c00e-4510-b440-9460550efeeb',

		'sandbox_app_id'  => 'app7029592943',
		'sandbox_app_key' => 'C27661098686A1EAA42C4F866FE974B2',
		'sandbox_token'   => 'A4C7607A317B419BB43DBB269A50D2EF',

		'url_sandbox_wp'  => 'https://ws.sandbox.pagseguro.uol.com.br',
		'url_producao_wp' => 'https://ws.pagseguro.uol.com.br',
		'url_sandbox'     => 'https://sandbox.pagseguro.uol.com.br',
		'url_producao'    => 'https://pagseguro.uol.com.br'
	];

	private $credentials;

	public function __construct(){
		parent::__construct();

		$this->credentials = [
			'email'  => $this->acessos['email'],
			'token'  => $this->acessos['token'],
			'url'    => $this->acessos['url_producao'],
			'wp'     => $this->acessos['url_producao_wp']
		];

		if(defined('SANDBOX') && !empty(SANDBOX)){
			unset($this->credentials);

			$this->credentials = [
				'email' => $this->acessos['email'],
				'token' => $this->acessos['sandbox_token'],
				'url'   => $this->acessos['url_sandbox'],
				'wp'    => $this->acessos['url_sandbox_wp']
			];
		}

	}

	public function index(){
		$front_controller = $this->get_controller('front');
		$front_controller->carregar_cabecalho_rodape();

		$cadastros = $this->get_model('produto')->carregar_cadastro();

		$contador = 1;
		$produtos = [];

		if(!empty($cadastros)){
			foreach($cadastros as $indice => $produto){

				$produto['preco'] = $produto['preco'] + ($produto['preco'] / 100 * 6);

				if(empty(strpos($produto['link'], 'http')) && strpos($produto['link'], 'http') != 0){
					$produtos[$contador][$indice]['link'] = 'http://' . $produto['link'];
				}

				$produtos[$contador][$indice] = $produto;
				$produtos[$contador][$indice]['preco'] = number_format($produtos[$contador][$indice]['preco'], 2, ',', '');

				if($contador == 5){
					$contador = 0;
				}

				$contador++;
			}
		}

		$this->view->assign('produtos', $produtos);
		$this->view->assign('timestamp', time());
		$this->view->render_plataforma('index');
	}

	public function comprar($id_produto){
		if(!isset($_GET['id'])){
			$timestamp = time();
			Header("Location: /index/comprar/{$id_produto[0]}?id={$timestamp}");
			exit;
		}

		$front_controller = $this->get_controller('front');
		$front_controller->carregar_cabecalho_rodape();

		$this->view->assign('id_produto', $id_produto[0]);
		$this->view->assign('timestamp', time());

		$this->view->render_plataforma('comprar');
		exit;
	}

	public function pagar(){
		$dados_pagamento  = carregar_variavel('checkout');
		$produto          = $this->get_model('produto')->carregar_cadastro($dados_pagamento['id_produto'])[0];
		$produto['preco'] = $produto['preco'] + ($produto['preco'] / 100 * 6);

		$usuario = [
			'usuario' => [
				'email'      => $dados_pagamento['email'],
				'hierarquia' => 3
			],
			'pessoa' => [
				'nome'      => $dados_pagamento['nome'],
				'sobrenome' => $dados_pagamento['sobrenome']
			]
		];

		$retorno_usuario = $this->get_controller('usuario')->insert_update($usuario);

		$venda = [
			'id_usuario' => $retorno_usuario['usuario']['retorno']['id'],
			'id_produto' => $dados_pagamento['id_produto'],
			'status'     => 1
		];

		$retorno_venda = $this->model->insert('venda', $venda);

		$curl = new \Curl\Curl();
		$curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
		$curl->setHeader('charset', 'SO-8859-1');

		$dados = [
			'email'                    => $this->credentials['email'],
			'token'                    => $this->credentials['token'],
			'currency'                 => 'BRL',
			'itemId1'                  => $produto['id'],
			'itemDescription1'         => $produto['nome'],
			'itemAmount1'              => number_format($produto['preco'], 2, '.', ''),
			'itemQuantity1'            => '1',
			'itemWeight1'              => (float) number_format($produto['peso'], 3, '.', '') * 100,
			'shipping.addressRequired' => 'false',
			'reference'                => $retorno_venda['id'],
			'senderName'               => $dados_pagamento['nome'] . ' ' . $dados_pagamento['sobrenome'],
			'senderAreaCode'           => substr(\Libs\Strings::remover_caracteres_especiais($dados_pagamento['telefone']), 0, 2),
			'senderPhone'              => substr(\Libs\Strings::remover_caracteres_especiais($dados_pagamento['telefone']), 02),
			'senderEmail'              => trim($dados_pagamento['email']),
			'redirectURL'              => 'http://chadepanela.felideo.com.br/' . $retorno_venda['id'] . '/',
			'timeout'                  => '60',
			'enableRecover'            => 'false'
		];

		$dados = carregar_trim($dados);

		$curl->post($this->credentials['wp'] . "/v2/checkout", $dados);

		$retorno = json_decode(json_encode($curl->response), true);

		Header('Location: ' . $this->credentials['url'] . '/v2/checkout/payment.html?code=' . $retorno['code']);
		exit;
	}

	public function postback(){
	    $retorno = $_POST;

	    $log['post'] = $retorno;

		$curl = new \Curl\Curl();
		$curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
		$curl->setHeader('charset', 'SO-8859-1');

		$acesso = [
			'email' => $this->credentials['email'],
			'token' => $this->credentials['token']
		];

		$curl = new \Curl\Curl();
		$curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
		$curl->setHeader('charset', 'SO-8859-1');
		$curl->get($this->credentials['wp'] . '/v3/transactions/notifications/' . $retorno['notificationCode'], $acesso);

		$retorno_consulta = (json_decode(json_encode($curl->response), true));


		$this->get_model('venda')->insert_update(
			'venda',
			['id' => $retorno_consulta['reference']],
			['status' => $retorno_consulta['status']],
			true
		);

		$log['consulta'] = $retorno_consulta;

		debug2($log);

		debug2($this->model->insert('debug', ['conteudo' => toText($log)]));
		http_response_code(200);
		exit;
	}

	public function compra_finalizada(){
		$this->view->render_plataforma('finalizada');
		exit;
	}

}