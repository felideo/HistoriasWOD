<?php

// include('../../../../configurar.php');
// include(DIR_RAIZ . '/app/gestaoja/helpers/funcoes.php');

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {

    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()) {
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

    function getName() {
        return $_GET['qqfile'];
    }

    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int) $_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }

}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {

    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
            return false;
        }
        return true;
    }

    function getName() {
        return $_FILES['qqfile']['name'];
    }

    function getSize() {
        return $_FILES['qqfile']['size'];
    }

}

class qqFileUploader {

    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760, $get, $files) {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($get)) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($files)) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings() {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));
    }

    private function toBytes($str) {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE) {
        if (!is_writable($uploadDirectory)) {
            return array('error' => "Erro no servidor. O destino não tem permissão de escrita.");
        }

        if (!$this->file) {
            return array('error' => 'Nenhum arquivo foi enviado.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'O arquivo está vazio.');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'O arquivo é muito grande.');
        }

        $pathinfo = pathinfo($this->file->getName());

        $filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'O arquivo não é válido, ele deve ter as seguintes extensões ' . $these . '.');
        }

        if (!$replaceOldFile) {
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {

            //ENVIANDO A IMAGEM DIRETAMENTE PARA O S3
            $local = $uploadDirectory . $filename . '.' . $ext;
            $local_prov = $uploadDirectory . uniqid() . '.' . $ext;
            copy($local, $local_prov);

            if (S3) {
                $remoto = "uploads/gestao_ja/imageblabs/" . $filename . '.' . $ext;
                $retorno = enviar_s3($local_prov, $remoto, 'public');
                unlink($local);
                unlink($local_prov);
            }

            gerar_thumb_max($uploadDirectory . $filename . '.' . $ext, $uploadDirectory . 'thumb_' . $filename . '.' . $ext, 300, 300);
            $local_thumb = $uploadDirectory . '/thumb_' . $filename . '.' . $ext;
            $local_thumb_prov = $uploadDirectory . '/thumb_' . uniqid() . '.' . $ext;
            @copy($local_thumb, $local_thumb_prov);

            if (S3) {
                $remoto_thumb = "uploads/gestao_ja/imageblabs/thumb_" . $filename . '.' . $ext;
                $retorno = enviar_s3($local_thumb_prov, $remoto_thumb, 'public');
                @unlink($local_thumb);
                @unlink($local_thumb_prov);
            }


            return [
                'success' => true,
                'filename' => $filename . '.' . $ext,
                'original_data' => $pathinfo,
                'size' =>  $size,
            ];
        } else {
            return array('error' => 'Impossível enviar o arquivo.' .
                'O envio foi cancelado, um erro no servidor foi encontrado.');
        }
    }

}

// lista de extensões válidas
$allowedExtensions = array();
// tamanho máximo em bytes
$sizeLimit = 10 * 1024 * 1024;

//####pala muito loca
error_reporting(0);
ini_set('display_errors', 'off');


print_r($allowedExtensions);

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit, $_GET['qqfile'], $_FILES['qqfile']);
$result = $uploader->handleUpload(DIR_RAIZ . '/app/webroot/uploads/gestao_ja/imageblabs/');
//print_r($result);
// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);