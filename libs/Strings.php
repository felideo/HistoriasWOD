<?php

namespace Libs;

abstract class Strings{
	// Remove espaçoes em branco no inicio e fim da string
	// Subistitui mais d um espaço junto por um unico espaço
	// Coloca tudo em letras minusculas
    static function limparString($string){
        return strtolower(trim(preg_replace('/\s+/', ' ',$string)));
    }

    //Retorna somente, numeros e  letras e hifens
    static function limpezaCompleta($string, $excecao = array(), $replace = ''){
    	$string = Strings::remover_caracteres_especiais($string, $excecao, $replace);
		$string = Strings::limparStringCompleto($string);
		$string = Strings::remover_acentos($string);
		$string = Strings::removerCaracteresMultiplicados('-', $string);

		return $string;
	}

    // Remove espaçoes em branco no inicio e fim da string
	// Subistitui mais d um espaço junto por um unico espaço
	// Coloca tudo em letras minusculas
	// Remove todos os acentos da string
	// Substitu espaço por underline
    static function limparStringCompleto($string, $caractere_replace = '-'){
    	return remover_acentos(str_replace(' ', $caractere_replace, self::limparString($string)));
    }

    static function somenteNumeros($string){
    	return str_replace('_', '', self::remover_caracteres_especiais(self::limparStringCompleto($string)));
    }

    static function removerCaracteresMultiplicados($caractere, $string){
    	return preg_replace('/' . $caractere . '+/', $caractere, $string);
    }

    static function limparFloat($string){

    	$float = self::removerCaracteresNaoNumericos(
    		self::remover_acentos(
    			self::remover_caracteres_especiais($string, [',', '.'])));

    	$float = trim($float);

    	if(stristr($float, '.') && stristr($float, ',')){
    		$float = str_replace('.', '', $float);
    	}

    	return (float) str_replace(',', '.', $float);
    }

    static function remover_caracteres_especiais($variavel, $excecao = array(), $replace = '') {
	    $a = array(',', '/', '(', ')', '-', '.', '&', '"', '\'', '|', '[', ']', '*', '#', '$', '%', '§', '´', '^', '+', '~', '?', '=', 'ª', 'º', ':', '!', '¡', '’', '–', '¿');
	    foreach ($excecao as $regra) {
	        $key = array_search($regra, $a);
	        unset($a[$key]);
	    }

	    $b = array($replace, $replace, $replace, $replace, $replace, $replace, 'e', $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace, $replace);

	    $variavel = str_replace($a, $b, $variavel);
	    return $variavel;
	}

	static function remover_acentos($texto) {
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'ç');
	    $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'c');

	    return str_replace($a, $b, $texto);
	}

	static function removerCaracteresNaoNumericos($string){
		return preg_replace("/[^0-9,.]/", "", $string);
	}
}