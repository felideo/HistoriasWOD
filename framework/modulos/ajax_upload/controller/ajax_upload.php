<?php
namespace ControllerCore;

class ajax_upload extends \Framework\Controller {
	public function upload($parametros = null) {
		$arquivo = carregar_variavel('qqfile');

		if(empty($arquivo['size'])){
			return array('error' => 'Arquivo em branco.');
		}

		$hash = \Libs\Hash::get_unic_hash();

		$pathinfo = pathinfo($arquivo['name']);
		$arquivo  = [
			'nome'     => $pathinfo['filename'],
			'extensao' => ".{$pathinfo['extension']}",
			'tamanho'  => (float) $arquivo['size'] / 1000000,
			'ativo'    => 1
		];

		$uploadname = $hash;

		if(!empty($pathinfo['extension'])){
			$uploadname .= ".{$pathinfo['extension']}";
		}

		if(!empty($_POST['manter_nome'])){
			$uploadname = $pathinfo['basename'];
		}

		$arquivo['endereco'] = "/uploads/{$_POST['local']}/{$uploadname}";

		$update = false;

		if(!empty($_POST['update'])){
			$update = true;
		}

		if(!is_dir('uploads/' . $_POST['local'])){
   			mkdir('uploads/' . $_POST['local'], 0777, true);
		}

		$retorno = move_uploaded_file($_FILES['qqfile']['tmp_name'], 'uploads/' . $_POST['local'] . '/' . $uploadname);

		if(empty($retorno)){
			$results = ['error' => 'Erroao fazer upload do arquivo.'];
		}

		@chmod($tempfilepath, 0644);

		if(empty($update)){
			$retorno_arquivo = $this->model->insert('arquivo', $arquivo);
		}else{
			$retorno_arquivo = $this->model->insert_update(
				'arquivo',
				['endereco' => $arquivo['endereco']],
				$arquivo,
				true
			);
		}

		$results = ['success' => true];
		$results = array_merge($results, array_merge($arquivo, $retorno_arquivo));

		$parametros[0] = $parametros[0] === 'true'? true: false;

		if(!empty($parametros[0]) && isset($results['success']) && !empty($results['success'])){
			$thumb = \Libs\PDFThumbnail::creatThumbnail($arquivo['endereco']);

			$explode = explode('/', $thumb);

			$insert_db_thumb = [
				'nome'     => end($explode),
				'endereco' => $thumb,
				'tamanho'  => (float) $size / 1000000,
				'extensao' => explode('.', end($explode))[1]
			];

			$retorno_thumb = $this->model->insert('arquivo', $insert_db_thumb);

			if(!empty($retorno_thumb['status'])){
				$insert_db_thumb['id_arquivo'] = $retorno_thumb['id'];
				$results['thumb'] = $insert_db_thumb;
			}

		}

		ob_clean();

		echo json_encode($results);
		exit;
	}
}
