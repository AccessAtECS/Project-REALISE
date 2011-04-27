<?php

class twitter extends feed {

	private $m_url;
	private $m_data;
	private $m_name;
	private $m_link;
	
	
	public function __construct($name){
		$this->m_url = "http://twitter.com/status/user_timeline/" . $name . ".json?count=5";
		$this->m_link = "http://twitter.com/$name/status/";
		$this->m_name = $name;
	}


	public function get(){
		$cache = new cache($this->m_url);
		
		$this->m_data = json_decode($cache->get(), true);

		$this->process();
	}
	
	private function process(){
		if(count($this->m_data) == 0) return;
		foreach($this->m_data as $tweet){
			$this->addItem("Tweet", $this->parseText($tweet['text']), $tweet['user']['name'], $this->m_link . $tweet['id_str'], $tweet['created_at']);
		}
	}
	
	private function parseText($input){
		$text = util::parseLinks($input);
		$text = preg_replace("/(#\S+)/i", '<a href="http://twitter.com/search?q=$1" target="_blank">$1</a>', $text);
		$text = preg_replace("/@(\S+)/i", '<a href="http://twitter.com/$1" target="_blank">@$1</a>', $text);
		return $text;
	}


}

?>