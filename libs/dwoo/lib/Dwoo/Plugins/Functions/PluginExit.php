<?php
namespace Dwoo\Plugins\Functions;

use Dwoo\Plugin;

class PluginExit extends Plugin{
    public function process(){
    	exit;
    }
}
