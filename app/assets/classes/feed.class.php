<?php

abstract class feed {

	protected $source;
	protected $items = array();

	protected function setName($name){
		$this->source = $name;
	}
	
	
	protected function addItem($title, $content, $author, $link, $time){
		array_push($this->items, array("title" => $title, "content" => $content, "author" => $author, "link" => $link, "time" => new DateTime($time, new DateTimeZone('Europe/London')), "source" => $this->source));
	}
	
	public function getItems(){
		return $this->items;
	}
	
	public function getName(){
		return $this->source;
	}

}

?>