<?
class controller_help extends controller {
	
	private $m_user;
	
	public function renderViewport() {
		$this->m_user = $this->objects("user");

		// Select the tab	
		util::selectTab($this->superview(), "community");	
		
		util::userBox($this->m_user, $this->superView());
				
		$this->superview()->replace("sideContent", "");
		
		$this->bindDefault('renderHelp');
	}
	
	protected function renderHelp(){
		$this->setViewport(new view("help"));	
		
		$this->pageName = "- Help";
	}
	

}