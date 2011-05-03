<?
class controller_community extends controller {
	
	private $m_user;
	private $m_innovators;
	private $m_currentIdea;
	
	public function renderViewport() {
		$this->m_user = $this->objects("user");

		// Select the tab	
		util::selectTab($this->superview(), "community");	
		
		util::userBox($this->m_user, $this->superView());
		
		$side = new view('communitySidebar');
		$side->append(new view('ideaLinks'));
		$side->append(new view('frag.sideInfo'));
				
		$this->superview()->replace("sideContent", $side);
		
		$this->bindDefault('renderCommunity');
	}
	
	protected function renderCommunity(){
		$this->setViewport(new view("community"));
		
		$users = new collection(collection::TYPE_USER);
		$users->setLimit(200);
		$users->setSort("id", collection::SORT_DESC);
		
		$userList = $users->get();
		

		$output = "";
		
		$template = new view("frag.innovator");
		
		foreach($userList as $innovator){
			if($innovator->getUsername() == ""){
				$template->replace("name", $innovator->getName());
			} else {
				$template->replace("name", "<a href='/profile/view/" . $innovator->getUsername() . "'>" . $innovator->getName() . "</a>");
			}
			
			$template->replace("tagline", $innovator->getTagline());
				
			$template->replace("src", $innovator->getPicture());
			
			$template->replace("img-size", 80);
			
			$output .= $template->get();
			
			$template->reset();
		}		
		

		
		$this->viewport()->replace("innovators", $output);
				
	}
	

}