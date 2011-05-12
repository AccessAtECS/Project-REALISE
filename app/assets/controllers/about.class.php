<?php

class controller_about extends controller {

	private $m_user;

	public function renderViewport() {
		$this->m_user = $this->objects("user");
		
		// Select the tab	
		util::selectTab($this->superview(), "home");	

		util::userBox($this->m_user, $this->superView());
				
		$this->superview()->replace("sideContent", util::displayNewInnovators());

		$this->bindDefault('aboutPage');
	}
	
	protected function aboutPage(){
		$this->setViewport(new view("homepage"));
		
		$this->viewport()->append(new view('about'));
	}



}

?>