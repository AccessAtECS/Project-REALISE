<?php

class controller_profile extends controller {

	private $m_user;

	public function renderViewport() {
		$this->m_user = $this->objects("user");
		
		// Select the tab	
		util::selectTab($this->superview(), "home");

		util::userBox($this->m_user, $this->superView());
				
		$this->superview()->replace("sideContent", util::displayNewInnovators());

		$this->bind('/update', 'saveProfile');

		$this->bindDefault('displayProfile');
	}
	
	protected function displayProfile(){
		$this->setViewport(new view("profile"));
		
		$this->viewport()->replaceAll(array(
			"profileImage" => $this->m_user->getPicture(),
			"tagline" => $this->m_user->getTagline(),
			"email" => $this->m_user->getEmail()
		));

	}

	protected function saveProfile(){
		print_r($_POST);
	}

}

?>