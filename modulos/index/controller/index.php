<?php
namespace Controller;

class Index extends \Framework\Controller {

	protected $modulo = [
		'modulo' 	=> 'index',
		'name'		=> 'Index',
		'send'		=> 'Index'
	];

	public function index(){
		echo 'Hellow World';
		exit;
	}
}