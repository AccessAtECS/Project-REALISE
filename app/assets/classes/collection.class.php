<?php 

class collection {

	private $m_type;
	private $m_returnArray = array();
	private $m_sort = "";
	private $m_limit = "";
	private $m_query = array();
	
	private $m_foundRows = 0;

	const SORT_DESC = 1;
	const SORT_ASC = 2;
	
	const TYPE_IDEA = 3;
	const TYPE_INCUBATED = 4;
	const TYPE_PROJECT = 5;
	const TYPE_USER = 6;
	const TYPE_TAG = 7;
	const TYPE_CATEGORY = 8;
	const TYPE_TOP_TAGS = 9;
	const TYPE_LICENSE = 10;
	const TYPE_COMMENT = 11;

	public function __construct($type){
		$type = (int)$type;
		if(is_int($type) == false) throw new Exception("Constructor argument type must be a valid type");
		if($type >= collection::TYPE_IDEA && $type <= collection::TYPE_COMMENT){
			$this->m_type = $type;
		} else {
			throw new Exception("The type specified is invalid");
		}
	}

	public function setLimit($first, $last = ""){
		$this->m_limit = ($last == "") ? " LIMIT $first" : " LIMIT $first, $last";
	}

	public function setSort($fieldName, $sortID){
		$sortID = (int)$sortID;
		if($sortID != collection::SORT_DESC && $sortID != collection::SORT_ASC) throw new Exception("Sort ID invalid.");
		
		switch($sortID){
		
			case collection::SORT_DESC:
				$this->m_sort = " ORDER BY " . $fieldName . " DESC";
			break;
			
			case collection::SORT_ASC:
				$this->m_sort = " ORDER BY " . $fieldName . " ASC";
			break;
		
		}
		
	}
	
	public function setQuery(array $arg){
		//array("", "fID", "=", $id)
		array_push($this->m_query, $arg);
	}
	
	public function get(){
		$db = db::singleton();
		
		switch($this->m_type){
			case collection::TYPE_IDEA:
				$db->select(array("id"), "idea", $this->m_query, $this->m_sort . $this->m_limit, "SELECT SQL_CALC_FOUND_ROWS %s FROM %s %s %s");
				$db->queuedQuery("SELECT FOUND_ROWS();");
				$output = $db->runBatch();

				if(empty($output)) return array();

				// Set the found rows.
				$this->m_foundRows = $output[1][0]['FOUND_ROWS()'];
				
				
				if(!empty($output[0])){
					foreach($output[0] as $idea){
						array_push($this->m_returnArray, new idea($idea['id']));
					}
				}
			break;
			
			case collection::TYPE_INCUBATED:
				$this->m_query = array_merge(array(array("", "incubated", "=", 1)), $this->m_query);
				$db->select(array("id"), "project", $this->m_query, $this->m_sort . $this->m_limit, "SELECT SQL_CALC_FOUND_ROWS %s FROM %s %s %s");
				$db->queuedQuery("SELECT FOUND_ROWS();");
				$output = $db->runBatch();
				
				if(empty($output)) return array();
				
				// Set the found rows.
				$this->m_foundRows = $output[1][0]['FOUND_ROWS()'];
				
				if(!empty($output[0])){
					foreach($output[0] as $project){
						array_push($this->m_returnArray, new project($project['id']));
					}
				}
			break;
			
			case collection::TYPE_PROJECT:
				$this->m_query = array_merge(array(array("", "incubated", "=", 0)), $this->m_query);
				$db->select(array("id"), "project", $this->m_query, $this->m_sort . $this->m_limit, "SELECT SQL_CALC_FOUND_ROWS %s FROM %s %s %s");
				$db->queuedQuery("SELECT FOUND_ROWS();");
				$output = $db->runBatch();
				
				if(empty($output)) return array();
				
				// Set the found rows.
				$this->m_foundRows = $output[1][0]['FOUND_ROWS()'];
				
				if(!empty($output[0])){
					foreach($output[0] as $project){
						array_push($this->m_returnArray, new project($project['id']));
					}
				}
			break;
			
			case collection::TYPE_USER:
				$db->select(array("id"), "user", $this->m_query, $this->m_sort . $this->m_limit);
				$output = $db->runBatch();
				if(empty($output)) return array();
				
				if(!empty($output[0])){
					foreach($output[0] as $user){
						array_push($this->m_returnArray, new user($user['id']));
					}
				}
			break;
			
			case collection::TYPE_TOP_TAGS:
				$sql = file_get_contents(SYS_ASSETDIR . "sql/toptags.sql") . $this->m_sort . $this->m_limit;
				$output = $db->single($sql);
				if(empty($output)) return array();
				
				return $output;
			break;
			
			case collection::TYPE_TAG:
				$db->select(array("id"), "tag", $this->m_query, $this->m_sort . $this->m_limit);
				$output = $db->runBatch();
				if(empty($output)) return array();
				
				if(!empty($output[0])){
					foreach($output[0] as $tag){
						array_push($this->m_returnArray, new tag($tag['id']));
					}
				}
			break;
			
			case collection::TYPE_LICENSE:
				$db->select(array("id"), "license", $this->m_query, $this->m_sort . $this->m_limit);
				$output = $db->runBatch();
				if(empty($output)) return array();
				
				if(!empty($output[0])){
					foreach($output[0] as $license){
						array_push($this->m_returnArray, new license((int)$license['id']));
					}
				}	
			break;
			
			case collection::TYPE_CATEGORY:
				$db->select(array("id"), "category", $this->m_query, $this->m_sort . $this->m_limit);
				$output = $db->runBatch();
				if(empty($output)) return array();
				
				if(!empty($output[0])){
					foreach($output[0] as $category){
						array_push($this->m_returnArray, new category((int)$category['id']));
					}
				}
			break;
			
			case collection::TYPE_COMMENT:
				$db->select(array("id"), "comment", $this->m_query, $this->m_sort . $this->m_limit);
				$output = $db->runBatch();
				if(empty($output)) return array();
				
				if(!empty($output[0])){
					foreach($output[0] as $comment){
						array_push($this->m_returnArray, new comment((int)$comment['id']));
					}
				}
			break;
			
			default:
				throw new Exception("Invalid type specified.");
			break;
		
		}
		
		return $this->m_returnArray;
	}
	
	public function getFoundRows(){
		return $this->m_foundRows;
	}
	
	public function getType(){
		return $this->m_type;
	}
	
	public function __toString(){
		return $this->get();
	}

}


?>