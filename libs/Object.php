<?php

namespace Libs;

class Object{
	public function to_array_1($obj){
		if(is_object($obj)){
			$obj = (array) $this->dismount($obj);
		}
		if(is_array($obj)) {
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = $this->to_array($val);
			}
		}else{
			$new = $obj;
		}
		return $new;
	}

	//permet de changer les private en public, pour un meuilleur conversion des object en array
	private function dismount($object) {
		$reflectionClass = new \ReflectionClass(get_class($object));
		$array = array();
		foreach ($reflectionClass->getProperties() as $property) {
			$property->setAccessible(true);
			$array[$property->getName()] = $property->getValue($object);
			$property->setAccessible(false);
		}
		return $array;
	}

	public function to_array_2($obj) {
	    //only process if it's an object or array being passed to the function
	    if(is_object($obj) || is_array($obj)) {
	        $ret = (array) $obj;
	        foreach($ret as &$item) {
	            //recursively process EACH element regardless of type
	            $item = $this->to_array_2($item);
	        }
	        return $ret;
	    }
	    //otherwise (i.e. for scalar values) return without modification
	    else {
	        return $obj;
	    }
	}
}