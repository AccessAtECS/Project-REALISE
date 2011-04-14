<?php 

class rss extends feed {

	private $m_name;
	private $m_raw;
	private $m_output;
	private $m_url;
	private $m_limit = 40;
	
	private $r_title;
	private $r_link;
	private $r_description;
	private $r_pubDate;
	
	private $r_items = array();
	
	public function __construct($name, $url){
		if(isset($name, $url) == false) throw new Exception("Name and URL must be set for RSS class");
		$this->m_name = $name;
		$this->m_url = $url;
	}
	
	public function setLimit($l){
		$this->m_limit = $l;
	}
	
	public function get(){
		$cache = new cache($this->m_url);
		
		$this->m_raw = $cache->get();
		$this->parse();
	}
	
	private function parse(){
	
		$sxml = new SimpleXMLElement($this->m_raw);
		
		$rss = $sxml->channel;
		
		$this->r_title = $rss->title;
		$this->r_link = $rss->link;
		$this->r_description = $rss->description;
		$this->r_pubDate = $rss->pubDate;
		
		$this->setName((string)$rss->title);
		
		$i = 0;
		
		foreach($rss->item as $item){
			array_push($this->r_items, array(
				"link" => $item->link,
				"title" => $item->title,
				"description" => $item->description
			));
			
			// Add to feed datastore
			$this->addItem((string)$item->title, (string)$item->description, "", (string)$item->link, (string)$item->pubDate);
			
			$i++;
			if($i == $this->m_limit) return;
		}
	}

	public function getFormatted(){
		$format = "<h3>{$this->r_title}</h3>";
		$itemFormat = "<div class=\"item\"><a href='[link]'>[title]</a><div class=\"description\">[description]</div></div>";
		
		$output = $format;
		
		foreach($this->r_items as $item){
			$b = $itemFormat;
			$b = str_replace(array("[link]", "[title]", "[description]"),array($item['link'], $item['title'], $item['description']), $b);
			
			$output .= $b;
		}
		
		return $output;
	
	}


}

?>