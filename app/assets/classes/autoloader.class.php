<?php

class autoloader {
    static public function register(){
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    static public function autoload($class_name){
		if(stristr($class_name, 'PEAR') !== false){
			$path = str_replace("_", "/", $class_name);
			include_once($class_name . ".php");
		} else {
			if(stristr($class_name, '_') !== false){
				$path = str_replace("_", "/", $class_name);
				include_once($path . ".php");
			} else {
				include_once($class_name . ".class.php");
			}
		
			if (!class_exists($class_name, false)) {
		   		trigger_error("Unable to load class: $class_name", E_USER_WARNING);
		  	}
		}
    }
}

?>