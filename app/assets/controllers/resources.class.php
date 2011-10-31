<?php

class controller_resources extends controller {

	private $m_user;


	public function renderViewport() {
		$this->m_user = $this->objects("user");

		// Select the tab	
		util::selectTab($this->superview(), "resources");	

		util::userBox($this->m_user, $this->superView());
		
		$side = new view("frag.sideInfo");
		$side->append(new view("frag.projectResources"));
		$side->append(new view('frag.presentations'));
		
		
		$this->superview()->replace("sideContent", $side);

		$this->bind('faq', 'faq');

		$this->bindDefault('resourcesLanding');	
	}
	
	
	protected function resourcesLanding(){
		$this->setViewPort(new view('resources'));
		
		$this->pageName = "- Resources";
		
		$emptech_categories = $this->emptechData();
		$this->viewport()->replace("emptech", $emptech_categories);
	}
	
	protected function faq(){
		$this->setViewPort(new view('faq'));
		
		$this->pageName = "- FAQ";
	}


	private function emptechData(){
		$e = new emptech();
		
		$e->getCategories();
		
		return $e->getFormattedCategories();
	}

}

?>