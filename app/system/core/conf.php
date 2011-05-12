<?php
// Define system-wide error reporting
error_reporting(E_ALL);

define("DEV", true);

define('SYS_MAILINGLIST_ADDR', "s@ecs.soton.ac.uk");

// Location of conf.php in infolio.
define("SYSTEM_DIR", "/var/www/dev/realise/htdocs/app/");
//define("SYSTEM_CONF", SYSTEM_DIR . "system/core/conf.php");

define('SYS_DEFAULTCNTRLR', 'home');

define('SYS_ROOTDIR', "/var/www/dev/realise/htdocs/");
define('SYS_REALBASEURL', 'http://realise.devx.co.uk/');
define('BASEURL', 'http://realise.devx.co.uk/');
define('SYS_INCLUDEURL', SYS_REALBASEURL . 'instep/');
define('SYS_CLASSDIR', SYS_ROOTDIR . "app/system/classes/");
define('SYS_SYSDIR', SYS_ROOTDIR . "app/system/");
define('SYS_ASSETDIR', SYS_ROOTDIR . "app/assets/");

define('SYS_INCLUDEPATHS', serialize(array(
	SYS_CLASSDIR,
	SYS_ASSETDIR . "classes/",
	SYS_ASSETDIR . "lib/",
	SYSTEM_DIR  . "system/"
)));

define('SYS_RESTFORMATS', serialize(array(
	"xml",
	"json"
)));

?>