<?php

class emptech {
	private $m_categoryList = "http://www.emptech.info/realise/category_list.xml";
	private $m_categoryData = array();
	
	private $m_raw;

	
	public function getCategories(){
		$cache = new cache($this->m_categoryList);
		
		$this->m_raw = $cache->get();
		$this->parseCategories();
		
		return $this->m_categoryData;
	}
	
	public function getFormattedCategories(){
		$output = "";
		
		
		foreach($this->m_categoryData as $k => $category){
			
			$output .= "<h4>$k</h4>\n<ul>";
			foreach($category as $subcat){
				$output .= "<li><a href='{$subcat['categoryURL']}'>{$subcat['subcategoryName']}</a></li>";
			}
			$output .= "</ul>";
			
		}
		
		return $output;
	}
	
	private function parseCategories(){
		$sxml = new SimpleXMLElement($this->m_raw);


		foreach($sxml->category as $category){
		
			$this->m_categoryData[(string)$category->categoryName][] = array(
				"subcategoryName" => (string)$category->subcategoryName,
				"categoryURL" => (string)$category->categoryURL
			);
		}
	
	}

}

?>