<?php

class openness {

	private $legal = -1;
	private $standards = -1;
	private $knowledge = -1;
	private $governance = -1;
	private $market = -1;
	private $openness = -1;
	
	
    public function set($name, $value) {
    	if(strstr($name, "_")) return;
        $value = rtrim($value, "%");
        $this->$name = (float) $value;
    }


    public function __get($name) {
        return round($this->$name);
    }


}

?>