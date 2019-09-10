<?php
namespace Controller;

class Reaper extends \Framework\ControllerCrud {
	protected $modulo = [
		'modulo' => 'reaper'
	];

	public function podchaser(){

		ini_set('max_execution_time', 0);
   		set_time_limit(0);

		$podcast_controller = $this->universe->get_controller('podcast');
		$url = 'https://www.podchaser.com/podcasts?page=';

		for ($i=200; $i < 400 ; $i++) {
			$html = file_get_contents($url . $i);
			$dom = \pQuery::parseStr($html);

			foreach ($dom->query('.topRow_cjo0m2') as $indice => $item) {
				$podcast = \pQuery::parseStr($item->html());

				$insert_update_db['podcast'] = [
					'nome'      => $podcast->query('.podcastTitle_1snunay')->attr('title'),
					'link'      => 'https://www.podchaser.com' . $podcast->query('.podcastTitle_1snunay')->attr('href'),
					'descricao' => \pQuery::parseStr($podcast->query('.description_c89eiy')->html())->query('div')->html()
				];

				$retorno[] = $podcast_controller->insert_update($insert_update_db);
			}
		}

		performance_stop();
		debug2($retorno);
		exit;

		debug2(get_class_methods($paginas));
		debug2($paginas);

		exit;

// _1aebyj8

		$paginas = \pQuery::parseStr($dom->query('.paginationui-container.last')->html());
		$paginas = $paginas->query('a');
		$paginas = $paginas->attr('href');

		$paginas = explode('/', $paginas);

		foreach($paginas as $indice => $item){
			if(empty($item)){
				unset($paginas[$indice]);
			}
		}

		debug2('ok');
		exit;
	}

	public function verificar_idioma_podcast($parametros){
		$podcast = $this->model->select('SELECT id, descricao FROM podcast WHERE id_idioma IS NULL ORDER BY RAND() LIMIT 1');

		if(!isset($podcast[0]['id']) || empty($podcast[0]['id'])){
			return false;
		}

		\DetectLanguage\DetectLanguage::setApiKey("6733c352e4fd0417d6b1dfd424814564");
		$retorno_idioma = \DetectLanguage\DetectLanguage::detect(substr($podcast[0]['descricao'], 0, 128));

		if(!isset($retorno_idioma[0]->language) || empty($retorno_idioma[0]->language)){
			return false;
		}

		$retorno['idioma'] = $this->model->insert_update(
			'idioma',
			['idioma' => $retorno_idioma[0]->language],
			['idioma' => $retorno_idioma[0]->language, 'ativo' => 1],
			false
		);

		if(empty($retorno['idioma']['id'])){
			return false;
		}

		$retorno['podcast'] = $this->model->insert_update(
			'podcast',
			['id' => $podcast[0]['id']],
			['id_idioma' => $retorno['idioma']['id']],
			true
		);

		debug2($retorno);
		exit;
	}
}