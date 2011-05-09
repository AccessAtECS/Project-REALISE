<?php

class Vault extends SplObjectStorage {

	private static $instance;

	// A private constructor; prevents direct creation of object
	private function __construct(){

	}
	
	public static function singleton() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

}

?>