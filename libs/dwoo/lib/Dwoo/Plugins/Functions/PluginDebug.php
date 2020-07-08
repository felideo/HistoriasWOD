<?php
namespace Dwoo\Plugins\Functions;

use Dwoo\Plugin;

class PluginDebug extends Plugin{
    public function process(){

    	debug2($this->core->getData());
		// $debug = new \Dwoo\Template\File($_SERVER['DOCUMENT_ROOT'] . '/vendor/dwoo/dwoo/lib/debug.html');
		// echo $this->core->get($debug, ['variaveis' => $this->core->getData()]);
    }
}
