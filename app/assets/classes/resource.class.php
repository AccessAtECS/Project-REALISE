<?php

abstract class resource extends dbo {

	const MEMBERSHIP_ADMIN = 1000;
	const MEMBERSHIP_USER = 1;
	const MEMBERSHIP_ANY = 0;
	
	const CACHE_TIMEOUT = 1440; // 1 day
	
	private $openness = null;
	private $totalVotes;

	public function addTags(resource &$obj, $tags){
		$tags = array_unique(explode(" ", trim($tags)));

		$resourceTags = $obj->getTags();
		print_r($resourceTags);

		foreach($tags as $tag){
			$tag = trim(($tag), ",");
			// See if the tag exists...
			try {
				$t = new tag($tag, tag::TYPE_NAME);
				$obj->addTag($t);
			} catch (Exception $e){
				if($e->getCode() == 404){
					// Tag doesn't exist. Create a new one.
					$t = new tag();
					$t->setName($tag);
					$t->commit();
					
					$obj->addTag($t);
				} else {
					//print_r($e->getTrace());// $e->getMessage();
					echo $e->getMessage();
				}
			}
		}

	}
	
	public function parseTags(resource $resource, view $t = NULL){
		if($t == NULL) $t = new view("frag.tag");
		// Deal with tags.
		$tags = $resource->getTags();
		
		if(count($tags) > 0){
			$o = new view();
			
			foreach($tags as $tag){
				$t->replace("tag", $tag->getName());
				$o->append( $t->get() );
				$t->reset();
			}
			
			return $o;
		} else {
			return "";
		}
		
	}
	
	public function hasVoted(user $user){
		$db = db::singleton();
		$check = $db->single("SELECT COUNT(*) FROM " . get_class($this) . "_user WHERE " . get_class($this) . "_id = '{$this->getId()}' AND user_id = '{$user->getId()}'");
		if($check[0]['COUNT(*)'] < 1) return false;
		return true;	
	}
	
	public function voteUp(user $user) {
		$db = db::singleton();
		$check = $db->single("SELECT id FROM " . get_class($this) . "_user WHERE " . get_class($this) . "_id = '{$this->getId()}' AND user_id = '{$user->getId()}'");
		if(!empty($check)) throw new Exception("This user has already up-voted this " . get_class($this) . ".");
		$db->single("INSERT INTO " . get_class($this) . "_user (" . get_class($this) . "_id, user_id) VALUES ('{$this->getId()}', '{$user->getId()}')");
		return true;
	}
	
	public function promoteUser(user $currentUser, user $promotionUser, $role = resource::MEMBERSHIP_USER){
		if(get_class($this) != "project") throw new Exception("This type of resource does not support user roles.");
		
		$db = db::singleton();
		$check = $db->single("SELECT id FROM " . get_class($this) . "_user WHERE " . get_class($this) . "_id = '{$this->getId()}' AND user_id = '{$promotionUser->getId()}'");
		if(empty($check)) throw new Exception("This user is not associated with this " . get_class($this) . ".");
		$db->single("UPDATE " . get_class($this) . "_user SET role = " . $db->real_escape_string($role) . " WHERE " . get_class($this) . "_id ='{$this->getId()}' AND user_id = '{$promotionUser->getId()}'");
		return true;
	}
	
	public function voteClear(user $user) {
		$db = db::singleton();
		$check = $db->single("SELECT id FROM " . get_class($this) . "_user WHERE " . get_class($this) . "_id = {$this->getId()} AND user_id = {$user->getId()}");
		if(empty($check)) throw new Exception("This user has not up-voted this " . get_class($this) . ".");
		$db->single("DELETE FROM " . get_class($this) . "_user WHERE " . get_class($this) . "_id = {$this->getId()} AND user_id = {$user->getId()}");
		return true;
	}
	
	public function countVotes($filter = resource::MEMBERSHIP_ANY) {
		$sql = "SELECT COUNT(*) AS count FROM " . get_class($this) . "_user WHERE " . get_class($this) . "_id = '{$this->getId()}'";
		if($filter != resource::MEMBERSHIP_ANY) $sql .= " AND role = " . $filter;
		
		if(empty($this->totalVotes)) {
			$db = db::singleton();
			$votes = $db->single($sql);
			$this->totalVotes = $votes[0]['count'];
		}
		return $this->totalVotes;
	}
	
	public function getVoters($filter = resource::MEMBERSHIP_ANY){
		$sql = "SELECT user_id FROM " . get_class($this) . "_user WHERE " . get_class($this) . "_id = '{$this->getId()}'";
		if($filter != resource::MEMBERSHIP_ANY) $sql .= " AND role = " . $filter;
		
		$db = db::singleton();
		$votes = $db->single($sql);
		
		$voters = array();
		
		foreach($votes as $voter){
			array_push($voters, new user((int)$voter['user_id']));
		}

		return $voters;		
	}
	
	private function getORating(){
		// First, see if we have a cached version (this is an expensive routine to run otherwise)
		
		$objectCache = new cache(get_class($this) . $this->getId(), cache::REQUEST_DATA, resource::CACHE_TIMEOUT);
		
		if($objectCache->has()){
			$cachedValue = $objectCache->get();
			if(is_object($cachedValue) && get_class($cachedValue) == "openness") $this->setOpennessRating($cachedValue);
		} else {
		
			$service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
			$client = Zend_Gdata_ClientLogin::getHttpClient(GDATA_USERNAME, GDATA_PASS, $service);
			$spreadsheetService = new Zend_Gdata_Spreadsheets($client);
			
			$query = new Zend_Gdata_Spreadsheets_ListQuery();
			$query->setSpreadsheetKey("tSS03UB1fQi6HgSZo6PWiMg");
			$query->setWorksheetId("od2");
			$query->setReverse('true');
			$query->setSpreadsheetQuery('_cn6ca = "' . $this->getName() . '"');
			
			$listFeed = $spreadsheetService->getListFeed($query);
			
			if(count($listFeed->entries) > 0){
			
				$rowData = $listFeed->entries[0]->getCustom();
				
				$or = new openness();
				
				foreach($rowData as $customEntry) {
					$or->set($customEntry->getColumnName(), $customEntry->getText());
				}
				
				$this->setOpennessRating($or);
			
			} else {
				$this->openness = false;
				$or = "";
			}
			
			$objectCache->put($or);
		}
	}
	
	private function setOpennessRating(openness $or){
		$this->openness = $or;
	}
	
	public function getOpennessRating(){
		if($this->openness == null) $this->getORating();
		return $this->openness;
	}


}

?>