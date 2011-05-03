<?php

class controller_admin extends controller {

	private $m_user;
	private $m_noRender = false;

	public function renderViewport() {
		$this->m_user = $this->objects("user");
		
		$this->bind("(?P<name>idea)/(?P<id>[0-9]+)/hide", "hide"); // Delete comment
		$this->bind("(?P<name>project)/(?P<id>[0-9]+)/hide", "hide"); // Delete comment
		
		$this->bindDefault('defaultHandler');
	}
	
	protected function hide($args){
		$this->m_noRender = true;
	
		if($this->m_user->getId() == null) throw new Exception("You do not have access to this area.");
	
		$object = new $args['name']($args['id']);
		$object->setHidden(TRUE);
		$object->commit();
		
		echo json_encode(array("status" => 200));
	}

	protected function noRender(){
		return $this->m_noRender;
	}
	
	protected function defaultHandler(){
		$this->m_noRender = false;
		
		// Select the tab	
		util::selectTab($this->superview(), "project");	

		util::userBox($this->m_user, $this->superView());		
		
		$this->superView()->replace('sideContent', '');
		
		$this->setViewport(new view('denied'));
	}

}

?>