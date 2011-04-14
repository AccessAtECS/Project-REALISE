<?php

class category {

	private $id = null;
	private $name;

	const TYPE_ID = 1;
	const TYPE_NAME = 2;

	function __construct($id, $type = category::TYPE_ID) {
		if($id == null) throw new Exception("No category ID provided");
		
		$db = db::singleton();
		
		switch($type){
		
			case category::TYPE_ID:
				if(is_int($id) == false) throw new Exception("Category ID is not an integer");
				$this->id = $id;
				$category = $db->single("SELECT * FROM category WHERE id = $this->id");
				if(empty($category)) throw new Exception("No category with that ID.", 404);
			break;
			
			case category::TYPE_NAME:				
				$category = $db->single("SELECT * FROM category WHERE name = '" . $db->real_escape_string($id) . "'");
				if(empty($category)) throw new Exception("No category with that ID.", 404);
				$this->id = $category[0]['id'];			
			break;
		
		
		}

		$this->name = $category[0]['name'];	
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}

}

?>