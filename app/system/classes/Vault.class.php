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

	final public function &objects($selection = null){
		if($selection == null) return $this;
		
		$this->rewind();
		while($this->valid()) {
		    $index  = $this->key();
		    $object = $this->current();
		    $data   = $this->getInfo();
		
			if($data == $selection){
				return $object;
			}
		    $s->next();
		}		
		
		return false;
	}

	final protected function setObject(&$newObj, $data = ""){
		if($this->contains($newObj)){
			$this->detach($newObj);
			$this->attach($newObj);
		} else {
			$this->attach($newObj, $data);
		}
		
	}

}

?>