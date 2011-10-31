<?php

class controller_profile extends controller {

	private $m_user;
	private $m_noRender = false;

	public function renderViewport() {
		$this->m_user = $this->objects("user");
		
		// Select the tab	
		util::selectTab($this->superview(), "home");

		util::userBox($this->m_user, $this->superView());
				
		$this->superview()->replace("sideContent", util::displayNewInnovators());

		$this->bind('view/(?P<profile_id>[a-zA-Z0-9\. ]+)', 'viewProfile');

		$this->bind('update', 'saveProfile');
		$this->bind('usernameCheck', 'checkUsername');

		$this->bindDefault('displayProfile');
	}
	
	protected function displayProfile(){
	
		if($this->m_user->getId() == null) throw new Exception("You do not have access to this area.");
		
		$this->setViewport(new view("profile"));
		
		$this->pageName = "- Profile";
		
		$this->viewport()->replaceAll(array(
			"profileImage" => $this->m_user->getPicture(),
			"tagline" => $this->m_user->getTagline(),
			"email" => $this->m_user->getEmail(),
			"username" => $this->m_user->getUsername(),
			"bio" => $this->m_user->getBio()
		));
		
		if($this->m_user->getEmailIsPublic()){
			$this->viewport()->replace('emailVisibility', "checked='checked'");
		} else {
			$this->viewport()->replace('emailVisibility', '');
		}
		
		$scripts = util::addScripts(array("/presentation/lib/ckeditor/ckeditor.js", "/presentation/lib/ckeditor/adapters/jquery.js", "/presentation/scripts/profile.js"));
		
		$this->superview()->replace("additional-assets", $scripts);
	}

	protected function saveProfile(){
		if($this->m_user->getId() == null) throw new Exception("You do not have access to this area.");
		
		if($_POST['username'] != "" && $_POST['username'] != $this->m_user->getUsername() && !user::usernameExists($_POST['username'])) $this->m_user->setUsername($_POST['username']);
		if($_POST['password'] != "" && md5($_POST['password'] != $this->m_user->getHash())) $this->m_user->setHash(md5($_POST['password']));
		$this->m_user->setTagline($_POST['newTagline']);
		$this->m_user->setEmail($_POST['newEmail']);
		if(isset($_POST['emailVisibility'])) $this->m_user->setEmailPublic($_POST['emailVisibility'] == "on" ? true : false);
		$this->m_user->setBio($_POST['bio']);
		

		if($_FILES['newImage']['name'] != ""){
			
			$picture = new image($_FILES['newImage']);
			$picture->resizeWidth = 80;
			$picture->resizeHeight = 80;
			$picture->move("users/" . md5($this->m_user->getId()) . "." . $picture->getFiletype());
			$this->m_user->setPicture($picture->getUrl());
		}
		
		$this->m_user->commit();
		
		$this->setObject(get_class($this->m_user), $this->m_user);
		$this->redirect("/profile");
	}
	
	protected function checkUsername(){
		if($this->m_user->getId() == null) throw new Exception("You do not have access to this area.");
		
		$this->m_noRender = true;

		if($this->m_user->getUsername() == $_POST['username']) {
			echo json_encode(array("status" => 200, "usernameExists" => true, "sameUsername" => true));
			return;
		}
		
		echo json_encode(array("status" => 200, "usernameExists" => user::usernameExists($_POST['username']), "sameUsername" => false));
	}
	
	protected function viewProfile($args){
		
		$this->setViewport(new view("viewProfile"));
		
		$user = new user(urldecode($args['profile_id']), user::ID_USERNAME);
		$this->viewport()->replaceAll(array(
			"username" => $user->getUsername(),
			"name" => $user->getName(),
			"tagline" => $user->getTagline(),
			"picture" => $user->getPicture(),
			"bio" => $user->getBio()
		));
		
		$this->pageName = "- Profile for " . $user->getUsername();

	}
	
	protected function noRender(){
		return $this->m_noRender;
	}

}

?>
