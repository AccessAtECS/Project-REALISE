<?php

class view {	

	/**
	 * @author [Seb Skuse]
	 * @copyright 2008
	 */	
	
	private $viewSource;
	private $identifier;
	private $source;
	
	const REPLACE_TEMP = 1;
	const REPLACE_CORE = 2;
	
	public function __construct($sourcefile = "", $identifier = ""){
		if($sourcefile == ""){
				$this->viewSource = "";
				$this->source = "";		
				$this->identifier = "";
		} else {
			$file = realpath( SYS_ASSETDIR . "views/" . $sourcefile . ".html");
			if(file_exists($file) == true){
				$this->viewSource = file_get_contents($file);
				$this->source = $this->viewSource;
				$this->identifier = ($identifier == "") ? $sourcefile : "";
			} else {
				throw new Exception("View not found! " . $file);
			}
		}
		return $this;
	}
	
	public function replace($var, $fragment, $type = view::REPLACE_TEMP){
		if(substr($var, 0, 1) == "#"){
			return $this->replaceById(substr($var, 1), $fragment, $type);
		}
		
		$this->viewSource = str_ireplace("{" . strtolower($var) . "}", $fragment, $this->viewSource);
		
		if($type == view::REPLACE_CORE) $this->source = $this->viewSource;
		return $this;
	}
	
	public function replaceById($id, $fragment, $type = view::REPLACE_TEMP){
		$this->viewSource = preg_replace("/<\w+\s?(?:id=[\"']{$id}[\"'])\s?\b[^>]*>(.*?)<\/\w+>/i", $fragment, $this->viewSource);
		
		if($type == view::REPLACE_CORE) $this->source = $this->viewSource;
		
		return $this;
	}
	
	public function replaceAll(array $data){
		foreach($data as $from => $to){
			$this->replace($from, $to);
		}
		return $this;
	}
	
	public function replaceWithStatic($var, $template, $path = ""){
		if($template == "") $template = "home";
		$fileStr = SYS_ASSETDIR . "views/" . $path . $template . ".html";
		if(file_exists($fileStr) == true){
			$this->viewSource = str_ireplace("{" . strtolower($var) . "}", file_get_contents($fileStr), $this->viewSource);
		} else {
			throw new Exception("View not found!");
		}
	}
	
	public function reset(){
		$this->viewSource = $this->source;
		return $this;
	}
	
	public function set($view){
		$this->viewSource = $view;
		$this->source = $view;
		return $this;
	}
	
	public function append($view){
		$this->viewSource .= $view;
		return $this;
	}
	
	public function setIdentifier($ident){
		$this->identifier = $ident;
		return $this;
	}
	
	public function get(){
		return $this->viewSource;
	}
	
	public function identify(){
		return $this->identifier;
	}
	
	public function __toString(){
		return $this->viewSource;
	}

}

?>