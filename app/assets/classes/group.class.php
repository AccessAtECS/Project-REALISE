<?php

class group extends dbo {

	private $id;
	private $name;

	public function __construct($id = null) {
		if($id == null) return;
		$this->id = $id;
		$db = db::singleton();

		$p = $db->single("SELECT * FROM `group` WHERE id = " . $db->real_escape_string($id));
		if(!empty($p)) {
			$this->name = stripslashes($p[0]['name']);
		} else {
			throw new Exception("No group with that ID!");
		}
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getOwner(){
		return new user(13);
	}
	
	public function getMembers(){
		
	}

}

?>