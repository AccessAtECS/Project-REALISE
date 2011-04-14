<?php
// Define system-wide error reporting
error_reporting(E_ALL);

define("DEV", true);
define("REALISE_VERSION", "0.6 (r732)");


// Location of conf.php in infolio.
define("SYSTEM_DIR", "/var/www/dev/realise/app/");
//define("SYSTEM_CONF", SYSTEM_DIR . "system/core/conf.php");

define('INSTEP_SYS_DEFAULTCNTRLR', 'home');

define('INSTEP_SYS_ROOTDIR', "/var/www/dev/realise/");
define('INSTEP_SYS_REALBASEURL', 'http://realise.devx.co.uk/');
define('INSTEP_BASEURL', 'http://realise.devx.co.uk/');
define('INSTEP_SYS_INCLUDEURL', INSTEP_SYS_REALBASEURL . 'instep/');
define('INSTEP_SYS_CLASSDIR', INSTEP_SYS_ROOTDIR . "app/system/classes/");
define('INSTEP_SYS_SYSDIR', INSTEP_SYS_ROOTDIR . "app/system/");
define('INSTEP_SYS_ASSETDIR', INSTEP_SYS_ROOTDIR . "app/assets/");

define('INSTEP_SYS_INCLUDEPATHS', serialize(array(
	INSTEP_SYS_CLASSDIR,
	INSTEP_SYS_ASSETDIR . "classes/",
	INSTEP_SYS_ASSETDIR . "lib/",
	SYSTEM_DIR  . "system/"
)));

define('INSTEP_SYS_RESTFORMATS', serialize(array(
	"xml",
	"json"
)));

?>