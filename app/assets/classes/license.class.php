<?php

class license {

	private $id = null;
	private $name;
	private $url;
	
	const TYPE_ID = 1;
	const TYPE_NAME = 2;

	public function __construct($id, $type = license::TYPE_ID){
		
		$db = db::singleton();
		
		if($id == 0){
			$this->id = 0;
			$this->name = "None Selected";
			return;
		}
		
		switch($type){
		
			case license::TYPE_ID:
				if(is_int($id) == false) throw new Exception("License ID is not an integer");
				$this->id = $id;
				$license = $db->single("SELECT * FROM license WHERE id = $this->id");
				if(empty($license)) throw new Exception("No license with that ID.", 404);
			break;
			
			case license::TYPE_NAME:				
				$license = $db->single("SELECT * FROM license WHERE name = '" . $db->real_escape_string($id) . "'");
				if(empty($license)) throw new Exception("No license with that ID.", 404);
				$this->id = $license[0]['id'];			
			break;
		
		
		}

		$this->name = $license[0]['name'];
		$this->url = $license[0]['url'];	
	}

	public function getName(){
		return $this->name;
	}
	
	public function getId(){
		return $this->id;
	}

	public function getUrl(){
		return $this->url;
	}

}

?>