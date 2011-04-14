<?
class tag {
	private $id = null;
	private $name;
	private $taggedProjects;
	private $taggedIdeas;
	
	const TYPE_ID = 1;
	const TYPE_NAME = 2;
	
	function __construct($id = null, $type = tag::TYPE_ID) {
		if($id == null) return;
		
		$db = db::singleton();
		
		switch($type){
		
			case tag::TYPE_ID:
				if(is_int($id) == false) throw new Exception("Tag ID is not an integer");
				$this->id = $id;
				$tag = $db->single("SELECT * FROM tag WHERE id = $this->id");
				if(empty($tag)) throw new Exception("No tag with that ID.", 404);
			break;
			
			case tag::TYPE_NAME:				
				$tag = $db->single("SELECT * FROM tag WHERE name = '" . $db->real_escape_string($id) . "'");
				if(empty($tag)) throw new Exception("No tag with that ID.", 404);
				$this->id = $tag[0]['id'];			
			break;
		
		
		}

		$this->name = $tag[0]['name'];	
	}
	
	public function commit(){
		$data['name'] = $this->name;

		$db = db::singleton();
		if($this->id == null) {
			$s = $db->insert($data, "tag");
		} else {
			$db->update($data, "tag", array(array("", "id", $this->id)));
		}
		$db->runBatch();
		if($this->id == null) $this->id = $db->insert_id;	
		
		return $this->id;
	}
	
	public function getProjects() {
		if(empty($this->taggedProjects)) {
			$db = db::singleton();
			$projects = $db->single("SELECT project_id FROM tag_project WHERE tag_id = $this->id");
			if(!empty($projects)) foreach($projects as $p) $this->taggedProjects[] = new project($p['project_id']);
		}
		return $this->taggedProjects;
	}
	
	public function getIdeas() {
		if(empty($this->taggedIdeas)) {
			$db = db::singleton();
			$ideas = $db->single("SELECT idea_id FROM tag_idea WHERE tag_id = $this->id");
			if(!empty($ideas)) foreach($ideas as $p) $this->taggedIdeas[] = new idea($p['idea_id']);
		}
		return $this->taggedIdeas;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($n){
		$this->name = $n;
	}
}