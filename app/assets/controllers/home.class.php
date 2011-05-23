<?php

class controller_home extends controller {
	
	private $m_user;
	private $m_noRender = false;
	private $errors = array();
	
	public function renderViewport(){
		// We want access to the user object.
		$this->m_user = $this->objects("user");

		$this->bind('login', 'loginFrag');
		$this->bind('register', 'registerFrag');
		$this->bind('updateImageCache', "updateImageCache");
		// Set the default request handler
		
		// Stray pages
		$this->bind('sitemap', 'sitemapPage');
		$this->bind('accessibility', 'accessibility');
		$this->bind('legal', 'legal');
		$this->bind('contact', 'contactPage');
		
		$this->bindDefault("homepageHandler");	
		
		$this->errors = array(
			34 => "Please login or register before submitting an idea."
		);
	}
	
	
	
	///////////////////////////////////////
	/*									 //
		Content generation functions	 //
	*/									 //
	///////////////////////////////////////
	
	
	protected function homepageHandler(){
		$latest = new view("frag.latest");
		
		$latestIdea = new collection(collection::TYPE_IDEA);
		$latestIncubated = new collection(collection::TYPE_INCUBATED);
		$latestProject = new collection(collection::TYPE_PROJECT);
		
		$latestIdea->setLimit(1);
		$latestIdea->setSort("id", collection::SORT_DESC);
		$idea = $latestIdea->get();
		
		$latestIncubated->setLimit(1);
		$latestIncubated->setSort("id", collection::SORT_DESC);
		$incubated = $latestIncubated->get();

		$latestProject->setLimit(1);
		$latestProject->setSort("id", collection::SORT_DESC);
		$project = $latestProject->get();

		
		$latest->replaceAll(array(
			"idea" => $idea[0]->getTitle(),
			"idea-id" => $idea[0]->getId()	
		));
		
		if(count($incubated) > 0){
			$latest->replaceAll(array(
				"incubated" => $incubated[0]->getName(),
				"incubated-id" => $incubated[0]->getId()
			));
		} else {
			$latest->replaceAll(array(
				"incubated" => "No Incubated Projects",
				"incubated-id" => ""
			));
		}
		
		$latest->replaceAll(array(
			"project" => $project[0]->getName(),
			"project-id" => $project[0]->getId()
		));


		$this->superview()->replace("sideContent", $latest . util::displayNewInnovators() );
		
		
		
		// Select the tab	
		util::selectTab($this->superview(), "home");

		// Display user box		
		util::userBox($this->m_user, $this->superView());

		// Set the viewport to the homepage.
		
		// Is the user logged in?
		$name = $this->m_user->getName();
		
		if($name == ""){
			$this->setViewport( new view("homepage") );
			$this->detectError();
		} else {
			$this->setViewport( new view("userHomepage") );
			
			$this->viewport()->replace('name', $this->m_user->getName());
			
			// Build the timeline.
			$timeline = new timeline();
			
			
			// Get OSSWatch blog articles
			$ossW = new rss('ossw', 'http://osswatch.jiscinvolve.org/wp/feed/');
			$ossW->setLimit(5);
			$ossW->get();
			
			$timeline->add($ossW);

			$accessECS = new rss('access', 'http://access.ecs.soton.ac.uk/blog/realise/feed/');
			$accessECS->setLimit(5);
			$accessECS->get();
			
			$timeline->add($accessECS);
			
			// Twitter
			$realisetweets = new twitter("projectrealise");
			$realisetweets->get();
		
			$timeline->add($realisetweets);

			$accesstweets = new twitter("accessatecs");
			$accesstweets->get();
		
			$timeline->add($accesstweets);			
			
			$activity = $timeline->getFormatted();
			
			$this->viewport()->replace('activityfeed', $activity);
			

		}
	
		
	}
	
	protected function updateImageCache(){
		if($this->m_user->getIsAdmin()){
			image::updateAll();
		}
		$this->redirect("/home");
		
	}
	
	protected function loginFrag(){
		$this->m_noRender = true;
		echo new view('frag.loginForm');
	}

	protected function registerFrag(){
		$this->m_noRender = true;
		echo new view('frag.registerForm');
	}
	
	protected function noRender(){
		return $this->m_noRender;
	}
	
	protected function detectError(){
		if(isset($_GET['e'])){
			$error = new view("frag.error");
			$error->replace('message', $this->errors[$_GET['e']]);
			
			$this->setViewport($error->append($this->viewport()));
		}	
	}
	
	protected function sitemapPage(){
		$this->setupPage();
		
		$this->setViewport(new view('sitemap'));
	}
	
	protected function accessibility(){
		$this->setupPage();
		
		$this->setViewport(new view('accessibility'));
	}

	protected function legal(){
		$this->setupPage();
		
		$this->setViewport(new view('legal'));
	}
	
	protected function contactPage(){
		$this->setupPage();
		
		$this->setViewport(new view('contact'));
	}
	
	protected function setupPage(){
		$this->superview()->replace("sideContent", util::displayNewInnovators() );
		
		// Select the tab	
		util::selectTab($this->superview(), "home");
		// Display user box		
		util::userBox($this->m_user, $this->superView());		
	}
	

}

?>