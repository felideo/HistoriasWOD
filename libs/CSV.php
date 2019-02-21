<?php
namespace Libs;

class CSV {
	public static function importarParaArray($caminhoArquivo, $separador, $verifica_separador = false) {
		if ($verifica_separador) {
			$separador = self::verifica_separador($caminhoArquivo);
		}

		if (empty($separador)) {
			throw new Exception('O caractere separador é obrigatório!');
		}

		$retorno = [];

		$arquivo = fopen($caminhoArquivo, 'r');

        while (!feof($arquivo) ) {
            $retorno[] = fgetcsv($arquivo, 1024, $separador);
        }

        fclose($arquivo);
        return $retorno;
	}

	public static function verifica_separador($caminhoArquivo){
		$delimitadores = [',', "\t", ';', '|', ':'];
		for ($i=0; $i < 2; $i++) {
			$linha = fgets(fopen($caminhoArquivo, 'r'),4096);
			foreach ($delimitadores as $delimitador) {
				$regExp = '/['.$delimitador.']/';
				$fields = preg_split($regExp, $linha);
				if(count($fields) > 1){
					if(!empty($resultado[$delimitador])){
						$resultado[$delimitador]++;
					}else{
						$resultado[$delimitador] = 1;
					}
				}
			}
		}
		return array_keys($resultado, max($resultado))[0];
	}

	public static function count_lines($caminhoArquivo){
		return count(file($caminhoArquivo));
	}
}