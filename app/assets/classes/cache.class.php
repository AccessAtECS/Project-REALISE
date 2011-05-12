<?php

class cache {

	private $m_data;
	private $m_url;
	
	private $m_reqtype;

	private $m_fingerprint;
	private $m_cacheFile;
	public $m_cacheInterval; // minutes

	const REQUEST_URL = 1;
	const REQUEST_DATA = 2;
	
	const DEFAULT_INTERVAL = 60;


	public function __construct($identifier, $type = cache::REQUEST_URL, $interval = cache::DEFAULT_INTERVAL){
		$this->m_identifier = $identifier;
		$this->m_url = $identifier;
		$this->m_reqtype = $type;
		$this->m_cacheInterval = $interval;
		
		$this->m_fingerprint = md5($identifier);
		$this->m_cacheFile = SYS_ASSETDIR . "cache/{$this->m_fingerprint}.tmp";
	}

	public function has(){
		if(file_exists($this->m_cacheFile)){
			$fInfo = filemtime($this->m_cacheFile);
			if( $fInfo != null && ( strtotime("+ " . $this->m_cacheInterval . " minutes", $fInfo) > time() ) ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	public function get(){
		if($this->m_reqtype == cache::REQUEST_URL){
			if(file_exists($this->m_cacheFile)){
				$fInfo = filemtime($this->m_cacheFile);
				if( $fInfo != null && ( strtotime("+ " . $this->m_cacheInterval . " minutes", $fInfo) > time() ) ) {
					return file_get_contents($this->m_cacheFile);
				}
			}
			
			$data = file_get_contents($this->m_url);
			file_put_contents($this->m_cacheFile, $data);
			return $data;
		} else {
			if($this->has()) {
				$data = file_get_contents($this->m_cacheFile);
				$object = unserialize($data);
				
				return ($object == FALSE) ? $data : $object;
			}
		
			return false;
		}
	}
	
	public function put($data){
		file_put_contents($this->m_cacheFile, serialize($data));
	}

}

?>