<?
class user extends dbo {
	private $id = null;
	private $linkedin_id = null;
	private $name;
	private $tagline = "";
	private $bio;
	private $picture = "";
	private $defaultPicture = "/presentation/images/avatar.png";
	private $email = "";
	private $emailPublic = false;
	private $username = "";
	private $hash = "";
	private $admin = 0;
	private $groups = array();
	
	private $mentorImage = "<img src='/presentation/images/star.png' class='tip mentor' title='This user is a mentor' style='float:right'>";
	
	private $authenticated = false;
		
	const ID_LOCAL = 1;
	const ID_LINKEDIN = 2;
	const LOCAL_ARRAY = 3;
	const USERNAME_CHECK = 4;
	const ID_USERNAME = 5;
	
	function __construct($id = null, $id_base = user::ID_LOCAL ) {
		if($id == null) return;

		$localQuery = "SELECT * FROM user WHERE id = %d";
		$linkedInQuery = "SELECT * FROM user WHERE linkedinID = '%s'";
		$arrayQuery = "SELECT * FROM user WHERE username= '%s' AND hash='%s'";
		$checkQuery = "SELECT COUNT(*) AS num FROM user WHERE username='%s'";
		$usernameQuery = "SELECT * FROM user WHERE username = '%s'";
		
		$db = db::singleton();
		
		switch($id_base){
			case user::ID_LOCAL:
				$this->id = $id;
				$p = $db->single(sprintf($localQuery, $this->id));
			break;
			
			case user::ID_LINKEDIN:
				$p = $db->single(sprintf($linkedInQuery, $id));
				if(!empty($p)){
					$this->id = $p[0]['id'];
				} else {
					throw new Exception("No user with that ID!");
				}
			break;
			
			case user::LOCAL_ARRAY:
				$username = $id['username'];
				$password = util::pass($id['password']);

				$p = $db->single(sprintf($arrayQuery, $username, $password));
				if(!empty($p)){
					$this->id = $p[0]['id'];
				} else {
					throw new Exception("Invalid Login", 604);
				}
			break;
			
			case user::ID_USERNAME:
				$p = $db->single(sprintf($usernameQuery, $id));
			break;
			
			case user::USERNAME_CHECK:
				$p = $db->single(sprintf($checkQuery, $id));
				if( (BOOL) $p[0]['num']) throw new Exception('User exists', 663);
				return;
			break;
			
			default:
				throw new Exception("Invalid ID base specified");
			break;
		}
		
		if(!empty($p)) {
			$this->id = (int)$p[0]['id'];
			$this->name = $p[0]['name'];
			$this->tagline = $p[0]['tagline'];
			$this->linkedin_id = $p[0]['linkedinID'];
			$this->picture = $p[0]['picture'];
			$this->email = $p[0]['email'];
			$this->username = $p[0]['username'];
			$this->hash = $p[0]['hash'];
			$this->admin = $p[0]['admin'];
			$this->emailPublic = (BOOL)$p[0]['emailPublic'];
			$this->bio = $p[0]['bio'];
			
			$this->getGroups();
			
		} else {
			throw new Exception("No user with that ID!");
		}
	}
	
	public function commit() {
		$data['name'] = $this->name;
		$data['tagline'] = $this->tagline;
		if($this->linkedin_id != null) $data['linkedinID'] = $this->linkedin_id;
		$data['picture'] = $this->picture;
		$data['email'] = $this->email;
		$data['username'] = $this->username;
		$data['hash'] = $this->hash;
		$data['emailPublic'] = (int)$this->emailPublic;
		$data['bio'] = $this->bio;
				
		$db = db::singleton();
		$check = $db->single("SELECT id FROM user WHERE id = '{$this->id}'");
		if(empty($check)) {
			$db->insert($data, "user");
		} else {
			$db->update($data, "user", array(array("WHERE", "id", $this->id)));
		}
		$db->runBatch();
		
		$id = $db->insert_id;
		if($this->id == null) $this->setId($id);
	}
	
	public function createAccount($name, $tagline, $linkedinID = ""){
		if(isset($name, $tagline)){
			$db = db::singleton();
			$sql = "INSERT INTO user (name, tagline, linkedinID) VALUES('" . $db->real_escape_string($name) . "', '" . $db->real_escape_string($tagline) . "', '" . $db->real_escape_string($linkedinID) . "')";
			
			$db->single($sql);
			
			$id = $db->insert_id;
			
			$this->setId($id);
			$this->setLinkedinId($linkedinID);
			$this->setName($name);
			$this->setTagline($tagline);
			
			return $id;
		} else {
			throw new Exception("Please set name and tagline.");
		}
	}
	
	public function getGroups(){
		
		if(count($this->groups) > 0) return $this->groups;
		
		$db = db::singleton();
		
		$res = $db->single("SELECT group.id AS id FROM user_group LEFT JOIN `group` ON user_group.group_id=group.id WHERE user_id={$this->getId()}");

		if(count($res) == 0) return;
		
		foreach($res[0] as $group){
			array_push($this->groups, new group((int)$group));
		}
		
		return $this->groups;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getLinkedinId(){
		return $this->linkedin_id;
	}
	
	public function setLinkedinId($i){
		$this->linkedin_id = $i;
	}

	public function setId($i){
		$this->id = $i;
	}
	
	public function setPicture($i){
		$this->picture = $i;
	}
	
	public function getPicture(){
		return ($this->picture == "") ? $this->defaultPicture : $this->picture;
	}

	public function getName() {
		return $this->name;
	}
	
	public function getHTMLName(){
		if($this->username != ""){
			$username = "<a href='/profile/view/{$this->getUsername()}'>{$this->getName()}</a>";
		} else {
			$username = $this->getName();
		}
		
		if(count($this->groups) == 0) return $username;
		
		foreach($this->groups as $group){
			if($group->getId() == 1){
				return $username . $this->mentorImage;
			}
		}
		
	}
	
	public function getTagline() {
		return $this->tagline;
	}
	
	public function setTagline($i){
		$this->tagline = $i;
	}

	public function getEmail(){
		return $this->email;
	}
	
	public function setEmail($e){
		$this->email = $e;
	}

	public function getOwner(){
		return $this;
	}

	public function setName($i) {
		$this->name = $i;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function setUsername($u){
		if(user::usernameExists($u)) throw new Exception("Username exists!");
		$this->username = $u;
	}
	
	public function getHash(){
		return $this->hash;
	}
	
	public function getIsAdmin(){
		return (BOOL) $this->admin;
	}
	
	public function setHash($h){
		$this->hash = $h;
	}
	
	public function setEmailPublic($visibility){
		$this->emailPublic = (BOOL)$visibility;
	}
	
	public function getEmailIsPublic(){
		return $this->emailPublic;
	}
	
	public function setBio($b){
		$this->bio = $b;
	}
	
	public function getBio(){
		return $this->bio;
	}
	
	public function canDelete(dbo $object){
		if($this->getId() == null) return false;
		
		if($this->getIsAdmin()) return true;
		
		$owner = $object->getOwner();
		
		// Are there multiple owners?
		if(is_array($owner)){
			foreach($owner as $user){
				if($user->getId() == $this->getId()) return true;
			}
		}
		
		// Is there one user?
		if(get_class($owner) == "user"){
			if($owner->getId() == $this->getId()) return true;
		}
		
		return false;
	}
	
	public function delete(dbo &$object){
		$db = db::singleton();
		
		if($this->canDelete($object)){
			$db->delete(get_class($object), array(array("", "id", $object->getId())));
			$db->runBatch();
			unset($object);
		} else {
			throw new Exception("You do not have adequate permissions to delete this " . get_class($object) . ".");
		}	
	}
	
	public function getEnrollment(resource $resource, $filter = resource::MEMBERSHIP_ANY){
		$db = db::singleton();
		
		$sql = "SELECT COUNT(*) AS enrolled FROM %s_user WHERE %1\$s_id = %d AND user_id = %d";

		if($filter != resource::MEMBERSHIP_ANY && get_class($resource) != "idea") $sql .= " AND role=" . (int)$filter;

		$res = $db->single(sprintf($sql, get_class($resource), (int)$resource->getId(), $this->id));

		return (BOOL)$res[0]['enrolled'];
	}
	
	/* Static functions */

	public static function usernameExists($username){
		$db = db::singleton();
		
		$sql = "SELECT COUNT(*) AS user_exists FROM user WHERE username = \"%s\"";
		$res = $db->single(sprintf($sql, $username));

		return (BOOL)$res[0]['user_exists'];
	}
	
}