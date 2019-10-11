<?php
namespace Libs;

class Image {
	private $file;
	private $size;
	private $width;
	private $height;
	private $folder;
	private $model;
	private $extension = array('jpg', 'png', 'gif');

	public function __set($property, $value)
	{
		$method = "set{$property}";
		if(method_exists($this, $method))
			return $this->$method($value);
	}

	/**
	 * método upload
	 * @param $file contém dos dados da imagem para upload
	 * @return $data com informação do nome do arquivo e pasta onde foi salvo
	 */
	public function upload($file) {

		$fileExtension = strtolower(end(explode('.', $file['imagem']['name'])));

		if (!in_array($fileExtension, $this->extension))
			throw new \Exception("Este arquivo não possui uma das extensões: jpg, png ou gif.");
		elseif ($file['imagem']['size'] > $this->size)
			throw new \Exception("O arquivo ultrapassou o limite máximo de {$this->size} bytes");

		if (!is_dir($this->folder)) {
			mkdir($this->folder);
		}

		$images = scandir($this->folder);
		$filename = $file['imagem']['name'];
		$i = 1;

		while (in_array($filename, $images)) {
			$image = preg_replace('/\.' . $fileExtension . '/', '', $filename);
			$filename = $image . '-' . $i . '.' . $fileExtension;
			$i++;
		}

		if (preg_match('/\//', $this->folder)) {
			$folders = explode('/', $this->folder);
			$this->folder = NULL;
			foreach ($folders as $folder) {
				if ($folder != '') {
					$this->folder .= $folder . '/';
				}
			}
		} else {
			$this->folder = $this->folder . '/';
		}

		$uploadFile = $this->folder . $filename;

		$data = array(
			'name'		=> $filename,
			'folder'	=> $this->folder
		);

		if (move_uploaded_file($file['imagem']['tmp_name'], $uploadFile)) {
			return $data;
		} else {
			return false;
		}
	}

	public function reap($url, $name, $folder){
		$folder = (new Folder())->check_folder($folder);

		if(empty(strpos('http', $url))){
			$url = 'http://' . ltrim($url, '//');

			if(empty($this->file_exists($url))){
				$url = str_replace('http://', 'https://', $url);
			}
		}

		if(empty($this->file_exists($url))){
			return [
			    'operacao'   => false,
			    'status'     => false,
			    'id'         => null,
			    'error_code' => null,
			    'erros_info' => 'Arquivo não existe e não pode ser baixado',
			];
		}

	    $file_info = new \finfo(FILEINFO_MIME_TYPE);
	    $mime_type = $file_info->buffer(file_get_contents($url));
	    $file_type = explode('/', $mime_type);
		$file_type = end($file_type);
		$name     .= ".{$file_type}";
		$image     = file_get_contents($url);

		file_put_contents($folder . '/' . $name, $image);

		if(filesize($folder . '/' . $name) == 0){
			unlink($folder . '/' . $name);
			return [
			    'operacao'   => false,
			    'status'     => false,
			    'id'         => null,
			    'error_code' => null,
			    'erros_info' => 'Arquivo não foi salvo no diretorio',
			];
		}

		$pathinfo   = pathinfo($folder . '/' . $name);
		$filename   = $pathinfo['filename'];
		$ext        = @$pathinfo['extension'];
		$ext        = ($ext == '') ? $ext : '.' . $ext;
		$hash       = Hash::get_unic_hash();
		$uploadname = $hash . $ext;
		$size       = number_format((float) filesize($folder . '/' . $name) / 1000000, '3', '.', '');

		$insert_db = [
			'hash'     => $hash,
			'nome'     => $filename,
			'endereco' => $folder . '/' . $name,
			'tamanho'  => $size * 1,
			'extensao' => $ext
		];

		$this->model = new \Framework\GenericModel();

		return $this->model->insert_update(
			'arquivo',
			['endereco' => $insert_db['endereco']],
			$insert_db,
			true
		);
	}

	public function file_exists($url) {
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_exec($ch);
	    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);

	    return $code == 200 || $code == 301;
	}

	/**
	 * método setSize
	 * @param $size Limite de KBs da imagem
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}

	/**
	 * método setWidth
	 * @param $width Limite da largura da imagem
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	}

	/**
	 * método setHeight
	 * @param $height Limite da altura da imagem
	 */
	public function setHeight($height)
	{
		$this->height = $height;
	}

	/**
	 * método setFolder
	 * @param $folder Contém o nome da pasta a ser definida para upload
	 */
	public function setFolder($folder)
	{
		$this->folder = IMG_FOLDER . $folder;
	}
}