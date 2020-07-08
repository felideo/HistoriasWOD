<?php
namespace ControllerCore;

class ajax_upload extends \Framework\Controller {
	public function upload($parametros = null) {
		debug2($_POST);
		// else do regular POST upload (i.e. for old non-HTML5 browsers)
		$size = $_FILES['qqfile']['size'];

		// if ($size == 0) {
		// 	return array('error' => 'File is empty.');
		// }

		$pathinfo   = pathinfo($_FILES['qqfile']['name']);
		$filename   = $pathinfo['filename'];
		$ext        = @$pathinfo['extension'];
		$ext        = ($ext == '') ? $ext : '.' . $ext;
		$hash       = !isset($_POST['nome']) || empty($_POST['nome']) ? \Libs\Hash::get_unic_hash() : $_POST['nome'];
		$uploadname = $hash . $ext;
		$update     = !isset($_POST['update']) || empty($_POST['update']) ? false : true;

		if(!is_dir('uploads/' . $_POST['local'])){
   			mkdir('uploads/' . $_POST['local']);
		}

		if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], 'uploads/' . $_POST['local'] . '/' . $uploadname)) {
			$results = ['error' => 'Could not save upload file.'];
		} else {
			@chmod($tempfilepath, 0644);

			$insert_db = [
				'hash'     => $hash,
				'nome'     => $filename,
				'endereco' => 'uploads/' . $_POST['local'] . '/' . $hash . $ext,
				'tamanho'  => (float) $size / 1000000,
				'extensao' => $ext

			];

			if(empty($update)){
				$retorno_arquivo = $this->model->insert('arquivo', $insert_db);
			}else{
				$retorno_arquivo = $this->model->insert_update(
					'arquivo',
					['endereco' => $insert_db['endereco']],
					$insert_db,
					true
				);
			}

			$results = array('success' => true);
			$results = array_merge($results, array_merge($insert_db, $retorno_arquivo));
		}

		if(!empty($parametros[0]) && isset($results['success']) && !empty($results['success'])){
			$thumb = Libs\PDFThumbnail::creatThumbnail($insert_db['endereco']);

			$explode = explode('/', $thumb);

			$insert_db_thumb = [
				'hash'     => explode('.', end($explode))[0],
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

		echo json_encode($results); // returns JSON
		exit;
	// return $results; // returns JSON
	}
}
