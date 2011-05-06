<?php
// Bootstrapper for REALISE application

// Bring in configuration
require_once("system/core/conf.php");

// Set the include paths.
$includePath = get_include_path() . ":" . implode(":", unserialize(INSTEP_SYS_INCLUDEPATHS));
set_include_path($includePath);

require_once("assets/classes/autoloader.class.php");

// Get the autoload functions
require_once("assets/configuration/init.php");

?>