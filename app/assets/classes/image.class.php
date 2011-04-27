<?php

class image {

	private $tmp_path;
	private $name;
	private $filetype;
	private $newpath;
	private $url;

	private $filetypes = array("png", "jpg", "jpeg", "gif");

	public function __construct($form_input){
		$this->name = $form_input['name'];
		$this->tmp_path = $form_input['tmp_name'];

		preg_match("/([^.]+)$/", $this->name, $matches);
		if(count($matches) == 0) throw new Exception("Filetype not detected");
		$this->filetype = strtolower($matches[0]);
		
		if(!in_array($this->filetype, $this->filetypes)) throw new Exception("Filetype not supported: " . $this->filetype);
		
	}
	
	public function move($to){
		$this->newpath = INSTEP_SYS_ROOTDIR . "site_assets/images/" . $to;
		$this->url = INSTEP_BASEURL . "site_assets/images/" . $to;
		if(move_uploaded_file($this->tmp_path, $this->newpath)){
			$this->resizeImage();
			
			return $this->url;		
		} else {
			echo "File upload failed.";
		}
	}
	
	private function resizeImage(){
		$im = new Imagick($this->newpath);
		$im->scaleImage(220, 100, true);
		$im->writeImage($this->newpath);
	}
	
	public static function updateAll(){
		$dir = INSTEP_SYS_ROOTDIR . "site_assets/images/";
		if ($dh = opendir($dir)) {
	        while (($file = readdir($dh)) !== false) {
	        	if($file == ".." || $file == ".") continue;
	        echo $dir . $file . "<br />";
	        	$im = new Imagick($dir . $file);
				$im->scaleImage(220, 100, true);
				$im->writeImage($dir . $file);
	        }
	        closedir($dh);
		}		

	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getFiletype(){
		return $this->filetype;
	}
	
	public function getUrl(){
		return $this->url;
	}

}

?>
