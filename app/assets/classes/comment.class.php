<?php

class comment extends dbo {

	private $id;
	private $body;
	private $author;
	private $timestamp = null;
	private $idea_id = null;
	private $project_id = null;
	private $view;
	private $image;

	public function __construct($id = null){
	
		$this->view = new view('frag.comment');
		$this->image = util::id(new view())->append("<img src=\"{picture}\" align=\"left\" height=\"50\" width=\"50\" alt=\"{author}'s picture\" class=\"roundItem\" />");
		if($id == null) return;
		
		$db = db::singleton();

		if(is_int($id) == false) throw new Exception("Comment ID is not an integer");
		$this->id = $id;
		$comment = $db->single("SELECT * FROM comment WHERE id = $this->id");
		if(empty($comment)) throw new Exception("No comment with that ID.", 404);
		
		$this->body = $comment[0]['body'];
		$this->author = new user($comment[0]['user_id']);
		$this->timestamp = new DateTime($comment[0]['time'], new DateTimeZone('UTC'));
		$this->idea_id = $comment[0]['idea_id'];
		$this->project_id = $comment[0]['project_id'];
	}
	
	public function commit(){
		$data['user_id'] = $this->author->getId();
		$data['body'] = $this->body;
		$data['time'] = $this->timestamp->format('Y-m-j H:i:s');
		if($this->idea_id != null) $data['idea_id'] = $this->idea_id;
		if($this->project_id != null) $data['project_id'] = $this->project_id;
		
		if($this->idea_id == null && $this->project_id == null ) throw new Exception("No target project / idea set.");
		
		$db = db::singleton();
		if($this->id == null) {
			$s = $db->insert($data, "comment");
		} else {
			$db->update($data, "comment", array(array("", "id", $this->id)));
		}
		$db->runBatch();
		if($this->id == null) $this->id = $db->insert_id;	
		
		return $this->id;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getOwner(){
		return $this->author;
	}
	
	public function setProjectId($id){
		$this->project_id = $id;
	}

	public function setIdeaId($id){
		$this->idea_id = $id;
	}
	
	public function setAuthor(user $author){
		$this->author = $author;
	}
	
	public function setBody($text){
		$this->body = $text;
	}
	
	public function setDate(DateTime $date){
		$this->timestamp = $date;
	}
	
	private function compile(user $currentUser){
		$delete = new view('frag.deleteComment');
		
		if($this->author->getUsername() != ""){
			$o = "<a href='/profile/view/{$this->author->getUsername()}'>" . $this->image . "</a>";
		} else {
			$o = $this->image;
		}
		
		$this->view->replace("image", $o);
		
		$this->view->replaceAll(array(
			"id" => $this->getId(),
			"author" => $this->author->getName(),
			"time" => $this->timestamp->format('l jS \of F Y, h:i a'),
			"picture" => $this->author->getPicture(),
			"body" => $this->format($this->body)
		));
		
		if($currentUser->canDelete($this)){
			// Display the deletion icon
			$this->view->replace('delete', $delete);
		} else {
			$this->view->replace('delete', '');
		}
		
	}

	private function format($text){
		// URL's
		$text = preg_replace("/(((f|ht){1}tps?:\/\/)[-a-zA-Z0-9@:%_\+.~#?!&\/\/=]+)/i", "<a href='$1'>$1</a>", $text);
		
		// Users
		$text = preg_replace("/[\w]*@([^\s]+)/i", "<a href='/profile/view/$1'>@$1</a>", $text);
		
		// Convert new lines to BR's
		$text = nl2br($text);
		
		return $text;
	}
	
	public function get(user $curentUser){
		$this->compile($curentUser);
		
		return $this->view->get();
	}

}


?>