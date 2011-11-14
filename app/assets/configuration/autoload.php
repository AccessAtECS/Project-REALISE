<?php

function getRuntimeObjects(){
	
	$objects = array(new user());
	
	return $objects;
}

// definitions for the app
define("REALISE_VERSION", "1.0.1 (r1083)");

require_once(realpath(INSTEP_SYS_ROOTDIR . "../system_configuration.php"));

// Set up database.
$db = db::singleton(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_SCHEMA);

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