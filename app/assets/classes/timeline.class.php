<?php

class timeline {

	private $sources = array();
	private $output;
	
	private $m_limit = 10;
	
	public function add(feed &$feed){
		$this->sources = array_merge($this->sources, $feed->getItems());
	}
	
	private function sort(){
		usort($this->sources, array("timeline", "cmp"));
	}
	
	static function cmp($a, $b){
        if ($a['time'] == $b['time']) {
            return 0;
        }
        return ($a['time'] < $b['time']) ? +1 : -1;
	}
	
	
	public function getFormatted(){
		$this->sort();
		
		$output = new view();
		$template = new view("frag.feed");
		
		foreach($this->sources as $k => $item){
			$template->replaceAll(array(
				"author" => $item['author'],
				"title" => $item['title'],
				"content" => $item['content'],
				"time" => $item['time']->format("l jS \of F Y"),
				"source" => $item['source'],
				"link" => $item['link']
			));
			
			$output->append($template);
			$template->reset();
		}
		
		return $output;
	}
	
	public function get(){
		$this->sort();
		return $this->sources;
	}

}

?>