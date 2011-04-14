<?php

function getRuntimeObjects(){
	
	$objects = array(new user());
	
	return $objects;
}

// definitions for the app
define('GDATA_USERNAME', "s@ecs.soton.ac.uk");
define('GDATA_PASS', 'hdRFyRX8H');

// This function auto loads classes.
function __autoload($class_name) {

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

?>