<?php
// Pull in configuration
require_once(realpath(INSTEP_SYS_ROOTDIR . "../system_configuration.php"));

// Register autoloaders here.
autoloader::register();
Github_Autoloader::register();


function getRuntimeObjects(){
	return array(new user());
}


// Set up database.
$db = db::singleton(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_SCHEMA);

// User defined constants for the app
define("REALISE_VERSION", "1.0.5 (r1091)");



?>