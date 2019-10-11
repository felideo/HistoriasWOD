<?php
namespace Libs;

class Folder {
	public function check_folder($folder){
		$return = true;

		if(!is_dir($folder)){
			$return = mkdir($folder, 0777, true);
		}

		if(empty($return)){
			return false;
		}

		return $folder;
	}
}