<?php

class controller_category extends controller {

	private $m_user;
	private $m_noRender = false;
	
	public function renderViewport() {
		$this->m_user = $this->objects("user");

		// Select the tab	
		util::selectTab($this->superview(), "home");	

		util::userBox($this->m_user, $this->superView());

		$this->superview()->replace("sideContent", "");

		$this->bind("(?P<id>[0-9]+)$", "renderCategoryList");

		$this->bindDefault('categoryIndex');
		
		$this->pageName = "- Categories";
	}
	
	
	
	protected function renderCategoryList($args){
		$cat_id = (int)$args['id'];
		
		try {
		
			$category = new category($cat_id);
			
			// Load the viewport.
			$this->setViewPort(new view('categoryOverview'));
			
			// Display the tag name on the page.
			$this->viewport()->replace('category', $category->getName());
			
			// Find the tag in the database.
			
			
			$projects = new collection(collection::TYPE_PROJECT);
			$ideas = new collection(collection::TYPE_IDEA);
			
			$projects->setQuery(array("AND", "category_id", "=", $cat_id));
			$ideas->setQuery(array("AND", "category_id", "=", $cat_id));
			
			$projects_array = $projects->get();
			$ideas_array = $ideas->get();
			
			$o = new view();
			
			if(count($ideas_array) > 0){
				foreach($ideas_array as $idea){
					$template = new resourceView($idea, $this->m_user);
					$o->append( $template->get() );
				}
				
				$this->viewport()->replace('ideasList', $o);
				$o->reset();
			} else {
				$this->viewport()->replace('ideasList', "");		
			}	
			
			if(count($projects_array) > 0){
				foreach($projects_array as $project){
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
	
	protected function categoryIndex(){
	
	}

}

?>