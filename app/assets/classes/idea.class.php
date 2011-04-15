<?
class idea extends resource {
	private $id;
	private $title;
	private $overview;
	private $description;
	private $owner_id;
	private $owner;
	private $image = "";
	private $defaultImage = "/presentation/images/placeholder_thumb.png";
	private $tags;
	private $category;
	private $comments;
	private $hidden;
	
	public function __construct($id = null) {
		if($id == null) return;
		$this->id = $id;
		$db = db::singleton();
		$p = $db->single("SELECT * FROM idea WHERE id = " . $db->real_escape_string($id));
		if(!empty($p)) {
			$this->title = stripslashes($p[0]['title']);
			$this->overview = stripslashes($p[0]['overview']);
			$this->description = stripslashes($p[0]['description']);
			$this->owner_id = $p[0]['user_id'];
			$this->image = $p[0]['image'];
			$this->category = new category((int)$p[0]['category_id']);
			$this->hidden = (BOOL) $p[0]['hidden'];
		} else {
			throw new Exception("No idea with that ID!");
		}
	}
	
	public function commit() {
		try {
		$data['title'] = $this->title;
		$data['overview'] = $this->overview;
		$data['description'] = $this->description;
		$data['user_id'] = $this->owner_id;
		$data['image'] = $this->image;
		$data['category_id'] = $this->category->getId();
		$data['hidden'] = $this->hidden;
		
		$db = db::singleton();
		$check = $db->single("SELECT id FROM idea WHERE id = '$this->id'");
		if(empty($check)) {
			$db->insert($data, "idea");
		} else {
			$db->update($data, "idea", array(array("WHERE", "id", $this->id)));
		}
		$db->runBatch();
		if($this->id == null) $this->id = $db->insert_id;	
		return $this->id;
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	public function addTag(tag $tag) {
		$db = db::singleton();
		$check = $db->single("SELECT id FROM tag_idea WHERE tag_id = {$tag->getId()} AND idea_id = $this->id");
		if(!empty($check)) throw new Exception("This idea already has that tag!");
		$db->single("INSERT INTO tag_idea (tag_id, idea_id) VALUES ({$tag->getId()}, $this->id)");
		$this->tags[] = $tag;
		return true;
	}

	public function removeTag(tag $tag) {
		$db = db::singleton();
		$check = $db->single("SELECT id FROM tag_idea WHERE tag_id = '{$tag->getId()}' AND idea_id = $this->id");
		if(empty($check)) throw new Exception("This idea does not have that tag!");
		$db->single("DELETE FROM tag_idea WHERE tag_id = {$tag->getId()} AND idea_id = $this->id)");
		foreach($this->tags as $i => $tag) if($tag->getId() == $tag->getId()) unset($this->tags[$i]);
		return true;
	}

	public function getTags() {
		if(empty($this->tags)) {
			$db = db::singleton();
			$tags = $db->single("SELECT tag_id FROM tag_idea WHERE idea_id = $this->id");
			if(!empty($tags)) foreach($tags as $tag) $this->tags[] = new tag((int)$tag['tag_id']);
		}
		return $this->tags;
	}

	public function getProjects(){
		$db = db::singleton();
		$i = $db->single("SELECT project_id FROM idea_project WHERE idea_project.idea_id=" . $db->real_escape_string($this->id));
		if(!empty($i)){
		
			$projects = array();
			
			foreach($i as $project){
				$id = (int)$project['project_id'];
			
				array_push($projects, new project($id));
			}
			
			return $projects;
		} else {
			throw new Exception("No results found for idea");
		}
	}
	

	public function setImage($image){
		if(empty($image['tmp_name'])) return;
		try {
			$image = new image($image);
			$project_image = $image->move(md5("idea-" . $this->id) . "." . $image->getFiletype());
			$this->image = $project_image;
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}

	public function getId(){
		return $this->id;
	}	
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getImage(){
		return empty($this->image) ? $this->defaultImage : $this->image;
	}
	

	public function setTitle($i) {
		$this->title = $i;
	}

	public function getOverview() {
		return $this->overview;
	}

	public function setOverview($i) {
		$this->overview = $i;
	}

	public function getHidden(){
		return $this->hidden;
	}
	
	public function setHidden($h){
		$this->hidden = (BOOL)$h;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($i) {
		$this->description = $i;
	}
	
	public function setCategory(category $category){
		$this->category = $category;
	}
	
	public function getCategory(){
		return $this->category;
	}
	
	public function getOwner() {
		if(empty($this->owner)) $this->owner = new user($this->owner_id);
		return $this->owner;
	}

	public function setOwner(user $i) {
		$this->owner = $i;
		$this->owner_id = $i->getId();
	}
}