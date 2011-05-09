<?php
// Pull in configuration
require_once(realpath(INSTEP_SYS_ROOTDIR . "../system_configuration.php"));

// Register autoloaders here.
autoloader::register();
Github_Autoloader::register();

// Set up objects

// Initialise db
$db = db::singleton(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_SCHEMA);

// Initialise the Vault
$Vault = Vault::singleton();

// Initialise objects
function getRuntimeObjects(){
	return array(new user());
}




// User defined constants for the app
define("REALISE_VERSION", "1.1.0 (r1091)");

?>