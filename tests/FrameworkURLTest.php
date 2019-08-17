<?php
namespace Test;
require './libs/functions.php';
use PHPUnit\Framework\TestCase;

class FrameworkURLTest extends TestCase {
	private $url;

	public function setUp() {
		$GLOBALS['_SERVER'] = [
			'HTTP_HOST'              => 'google.com',
			'REQUEST_URI'            => '/index/index?test1=1teste2=2',
			'HTTP_X_FORWARDED_PROTO' => 'https',
			'HTTPS'                  => 'on',
		];

		$this->url = new \Framework\URL();

	}

	public function test_get_url(){
		$this->assertTrue(is_string($this->url->get_url()));
	}
	public function test_get_parsed(){
		$url_parsed = $this->url->get_parsed();

		$this->assertTrue(is_array($url_parsed));
		$this->assertArrayHasKey('scheme', $url_parsed);
		$this->assertArrayHasKey('host', $url_parsed);
		$this->assertArrayHasKey('path', $url_parsed);
		$this->assertArrayHasKey('query', $url_parsed);
	}
	public function test_get_scheme(){
		$this->assertTrue(is_string($this->url->get_scheme()));
	}
}