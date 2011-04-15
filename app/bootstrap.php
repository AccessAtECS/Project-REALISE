<?php
// Bootstrapper for REALISE application

// Bring in configuration
require_once("system/core/conf.php");

// Set the include paths.
$includePath = get_include_path() . ":" . implode(":", unserialize(INSTEP_SYS_INCLUDEPATHS));
set_include_path($includePath);

// Get the autoload functions
require_once("assets/configuration/autoload.php");

?>