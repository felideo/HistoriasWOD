<?php
namespace Dwoo\Plugins\Functions;

use Dwoo\Plugin;

class PluginDebug extends Plugin{
    public function process(){
    	debug2($this->core->getData());
    }
}
