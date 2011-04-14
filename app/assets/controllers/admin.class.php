<?php

class controller_admin extends controller {

	private $m_user;

	public function renderViewport() {
		$this->m_user = $this->objects("user");
		
		$this->bind("idea/(?P<id>[0-9]+)/delete", "deleteIdea"); // Delete comment
		$this->bind("project/(?P<id>[0-9]+)/delete", "deleteProject"); // Delete comment
		
		$this->bindDefault('aboutPage');
	}
	
	protected function deleteIdea(){
		
	}

	protected function deleteProject(){
		
	}

	protected function noRender(){
		return true;
	}

}

?>