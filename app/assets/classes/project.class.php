<?
class project extends resource {
	private $id = null;
	private $name;
	private $overview = "";
	private $description = "";
	private $url = "";
	private $license = null;
	private $members;
	private $tags = array();
	private $idea;
	private $incubated;
	private $image = "";
	private $defaultImage = "/presentation/images/placeholder_thumb.png";
	private $category;
	private $community_url = "";
	private $scm_url = "";
	private $repo_url = "";
	private $hidden;
	
	const ROLE_ANY = 0;
	const ROLE_USER = 1;
	const ROLE_ADMIN = 1000;
	
	function __construct($id = null) {
		if($id == null) return;
		$this->id = $id;
		$db = db::singleton();
		$p = $db->single("SELECT * FROM project WHERE id = " . $db->real_escape_string($id));
		if(empty($p)) throw new Exception("No project with that ID!");
		$this->name = stripslashes($p[0]['name']);
		$this->url = $p[0]['url'];
		$this->license = new license((int)$p[0]['license']);
		$this->incubated = $p[0]['incubated'];
		$this->overview = stripslashes($p[0]['overview']);
		$this->description = stripslashes($p[0]['description']);
		if(isset($p[0]['image'])) $this->image = $p[0]['image'];
		$this->category = new category((int)$p[0]['category_id']);
		
		$this->hidden = (BOOL) $p[0]['hidden'];
		
		$this->community_url = $p[0]['community_url'];
		$this->scm_url = $p[0]['scm_url'];
		$this->repo_url = $p[0]['repo_url'];
		
		$this->getMembers();
	}
	
	public function commit() {
		$data['name'] = $this->name;
		$data['overview'] = $this->overview;
		$data['description'] = $this->description;
		$data['url'] = $this->url;
		$data['license'] = $this->license;
		$data['incubated'] = (int)$this->incubated;
		$data['image'] = $this->image;
		$data['category_id'] = $this->category->getId();
		$data['community_url'] = $this->community_url;
		$data['scm_url'] = $this->scm_url;
		$data['repo_url'] = $this->repo_url;
		$data['hidden'] = $this->hidden;
		if($this->license != null) {
			$data['license'] = $this->license->getId();
		} else {
			$data['license'] = 0;
		}
		
		$db = db::singleton();
		$check = $db->single("SELECT id FROM project WHERE id='$this->id'");
		
		if(empty($check)) {
			$db->insert($data, "project");
		} else {
			$db->update($data, "project", array(array("WHERE", "id", $this->id)));
		}
		$db->runBatch();
		if($this->id == null) $this->id = $db->insert_id;	
		return $this->id;	
	}

	public function getSiblings(){
		
	}
	
	public function getSiblingCount(){
		$sql = "SELECT COUNT(*) AS count FROM idea_project WHERE idea_id = {$this->getIdea()->getId()} AND project_id <> {$this->getId()}";
		
		$db = db::singleton();
		$siblings = $db->single($sql);
		
		return (int)$siblings[0]['count'];
	}

	public function getMembers($filter = project::ROLE_ANY) {

		$sql = "SELECT * FROM project_user pu JOIN user u ON (pu.user_id = u.id) WHERE pu.project_id = '$this->id'";
		
		if($filter != project::ROLE_ANY) $sql .= " AND role = " . $filter;
		
		$this->members = array();
		
		$db = db::singleton();
		$members = $db->single($sql);
		foreach($members as $m) $this->members[] = new user($m['id']);

		return $this->members;
	}
	
	public function getOwner(){
		return $this->getMembers(project::ROLE_ADMIN);
	}
	
	public function getIdea(){
		$db = db::singleton();
		$i = $db->single("SELECT idea_id FROM idea_project WHERE idea_project.project_id=" . $db->real_escape_string($this->id));
		if(!empty($i)){
			$id = (int)$i[0]['idea_id'];
			$this->idea = new idea($id);
			
			return $this->idea;
		} else {
			throw new Exception("No results found for idea");
		}
	}

	public function hasMember(user $i){
		if(!empty($this->members)) foreach($this->members as $m) if($m->getId() == $i->getId()) return true;
		return false;
	}

	public function addMember(user $i, $role = project::ROLE_USER) {
		// Validate user object type and that user isn't already in this project.
		if(!is_a($i, "user")) throw new exception("Parameter was not a valid user object.");
		if(!empty($this->members)) foreach($this->members as $m) if($m->getId() == $i->getId()) throw new Exception("Member is already involved with this project.");
		$db = db::singleton();
		$db->single("INSERT INTO project_user (project_id, user_id, role) VALUES ('$this->id', '{$i->getId()}', " . (int)$role . ")");
		$this->members[] = $i;
		return true;
	}
	
	public function removeMember(user $i) {
		// Validate user object type and that user is already in this project.
		if(!is_a($i, "user")) throw new exception("Parameter was not a valid user object.");
		if(!empty($this->members)) foreach($this->members as $k => $m) if($m->getId() == $i->getId()) {
			$db = db::singleton();
			$db->single("DELETE FROM project_user WHERE project_id = '$this->id' AND user_id = '{$i->getId}'");
			unset($this->members[$k]);
			return true;
		} else {
			throw new Exception("That member is not involved with this project.");
		}
	}

	public function addTag(tag $tag) {
		$db = db::singleton();
		$check = $db->single("SELECT id FROM tag_project WHERE tag_id = {$tag->getId()} AND project_id = $this->id");
		if(!empty($check)) return 377;
		$db->single("INSERT INTO tag_project (tag_id, project_id) VALUES ({$tag->getId()}, $this->id)");
		$this->tags[] = $tag;
		return true;
	}
	
	public function removeTag(tag $tag) {
		$db = db::singleton();
		$check = $db->single("SELECT id FROM tag_project WHERE tag_id = {$tag->getId()} AND project_id = $this->id");
		if(empty($check)) throw new Exception("This project does not have that tag!", 377);
		$db->single("DELETE FROM tag_project WHERE tag_id = {$tag->getId()} AND project_id = $this->id");
		foreach($this->tags as $i => $tag) if($tag->getId() == $tag->getId()) unset($this->tags[$i]);
		return true;
	}
	
	public function getTags() {
		if(empty($this->tags)) {
			$db = db::singleton();
			$tags = $db->single("SELECT tag_id FROM tag_project WHERE project_id = $this->id");
			if(!empty($tags)) foreach($tags as $tag) $this->tags[] = new tag((int)$tag['tag_id']);
		}
		
		return $this->tags;
	}


	public function getId(){
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($i) {
		$this->name = $i;
	}
	
	public function setOverview($o){
		$this->overview = $o;
	}
	
	public function getOverview(){
		return $this->overview;
	}
	
	public function setDescription($d){
		$this->description = $d;
	}
	
	public function getDescription(){
		return $this->description;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($i) {
		$this->url = $i;
	}

	public function getLicense() {
		return $this->license;
	}
	
	public function setImage($i){
		if(empty($i['tmp_name'])) return;
		try {
			$image = new image($i);
			$project_image = $image->move(md5("proj-" . $this->id) . "." . $image->getFiletype());
			
			$this->image = $project_image;
		} catch(Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
	
	public function getImage(){
		return !empty($this->image) ? $this->image : $this->defaultImage;
	}

	public function setCategory(category $category){
		$this->category = $category;
	}
	
	public function getCategory(){
		return $this->category;
	}
	
	public function getIncubated(){
		return (int)$this->incubated;
	}
	
	public function getCommunityUrl(){
		return $this->community_url;
	}
	
	public function setCommunityUrl($url){
		$this->community_url = $url;
	}
	
	public function getScmUrl(){
		return $this->scm_url;
	}
	
	public function setScmUrl($url){
		$this->scm_url = $url;
	}
	
	public function getRepoUrl(){
		return $this->repo_url;
	}
	
	public function setRepoUrl($url){
		$this->repo_url = $url;
	}
	
	public function getHidden(){
		return $this->hidden;
	}
	
	public function setHidden($h){
		$this->hidden = (BOOL)$h;
	}
	
	public function getPromotionStatus(){
		if(!$this->incubated) return false;
		
		//if($this->licence != "" && $this->description != "")
		
		return false;
	}
	
	public function getPromotionPercentage(){
		$or = new opennessrating($this->id);
		
		return (int)$or->getScore();
		
		/*$criteria = array($this->url, $this->license, $this->community_url, $this->scm_url, $this->repo_url);
		$step = ceil(100 / count($criteria));
		$progress = 0;
		foreach($criteria as $c){
			if($c != "") $progress += $step;
		}
		return ceil($progress);*/
	}
	
	public function setIdea(idea $idea){
		if(empty($this->id)) throw new Exception("Project must be committed to the database before setting the idea.");
		$db = db::singleton();
		$db->single("INSERT INTO idea_project (idea_id, project_id) VALUES ('{$idea->getId()}', '{$this->id}')");
		$this->idea = $idea;
		return true;
	}

	public function setIncubated($i){
		$this->incubated = $i;
	}

	public function setLicense(license $i) {
		$this->license = $i;
	}
}