<?php

class controller_tag extends controller {

	private $m_user;

	public function renderViewport() {
		$this->m_user = $this->objects("user");

		// Select the tab	
		util::selectTab($this->superview(), "home");	

		util::userBox($this->m_user, $this->superView());
				
		$this->superview()->replace("sideContent", util::displayNewInnovators());

		$this->bind("[0-9a-zA-Z]+$", "projectsWithTag");

		$this->bindDefault('tagCloud');
	}

	
	
	protected function projectsWithTag(){
		$tagName = end($this->context());
		
		// Load the viewport.
		$this->setViewPort(new view('tagOverview'));
		
		// Display the tag name on the page.
		$this->viewport()->replace('tag', $tagName);
		
		$this->pageName = "- things tagged $tagName";
		
		// Find the tag in the database.
		try {
		$tag = new tag($tagName, tag::TYPE_NAME);
		
		$projects = $tag->getProjects();
		$ideas = $tag->getIdeas();
		
		$o = new view();
		
		if(count($ideas) > 0){
			foreach($ideas as $idea){
				$template = new resourceView($idea, $this->m_user);
				$o->append( $template->get() );
			}
			
			$this->viewport()->replace('ideasList', $o);
			$o->reset();
		} else {
			$this->viewport()->replace('ideasList', "");		
		}	
		
		if(count($projects) > 0){
			foreach($projects as $project){
				$template = new resourceView($project, $this->m_user);
				
				$o->append( $template->get() );		
			}
			
			$this->viewport()->replace('projectList', $o);
			$o->reset();
		} else {
			$this->viewport()->replace('projectList', "");		
		}
		
		} catch(Exception $e){
			$o = new view();
			$o->append($e->getMessage());
			$this->setViewPort($o);
		}
	}
	
	
	protected function tagCloud(){
		$this->setViewPort(new view('tagExplorer'));
		
		$collection = new collection(collection::TYPE_TOP_TAGS);
		$collection->setLimit(10);
		
		$t = new view();
		$o = new view();
		$t->set("<li><a href=\"tag/{tag}\">{tag}</a> ({count})</li>");
		
		foreach($collection->get() as $tag){
			$t->replace("tag", $tag['name']);
			$t->replace("count", $tag['count']);
			$o->append($t);
			$t->reset();
		}
		
		$this->viewport()->replace("toptags", $o);
		
	}


}

?>