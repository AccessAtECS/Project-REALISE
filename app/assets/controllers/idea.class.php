<?
class controller_idea extends controller {
	
	private $m_user;
	private $m_innovators;
	private $m_currentIdea;
	private $m_ideaOwner;
	private $m_noRender = false;
	private $m_owner = false;
	
	public function renderViewport() {
		$this->m_user = $this->objects("user");

		// Select the tab	
		util::selectTab($this->superview(), "idea");	
		
		util::userBox($this->m_user, $this->superView());
		
		
		$this->bind("new$", "newIdea"); // Add a new idea (form)
		$this->bind("new/add", "addIdea"); // Add a new idea (process)
		
		$this->bind("[0-9]+$", "renderIdea"); // View a specific idea.
		$this->bind("[0-9]+/vote$", "vote"); // DATA - vote on an idea
		
		$this->bind("(?P<id>[0-9]+)/comment$", "comment"); // Comment on an idea
		$this->bind("(?P<id>[0-9]+)/comment/(?P<comment_id>[0-9]+)/delete", "deleteComment"); // Delete comment
		
		$this->bind("(?P<id>[0-9]+)/incubate$", "incubateIdea"); // Display the incubate form.
		$this->bind("(?P<id>[0-9]+)/incubate/confirm", "processIncubation"); // Make a new project with this info set to incubated.
		
		$this->bind("(?P<id>[0-9]+)/admin$", "ideaAdmin"); // Administer an idea.
		$this->bind("(?P<id>[0-9]+)/admin/update$", "adminSave"); // Administer an idea.
		

		$this->bindDefault('renderIdeasLab');
	}
	
	protected function renderIdeasLab(){
		$this->setViewport(new view("ideasLab"));

		$side = new view('frag.filters');
		$side->append(new view('ideaLinks'));
		$side->append(new view('frag.sideInfo'));
		$side->replace("categories", util::getCategories());
		$this->superview()->replace("sideContent", $side);

		$search = isset($_GET['search']) ? $_GET['search'] : "";
		$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
		
		$ideas = new collection(collection::TYPE_IDEA);
		$ideas->setLimit(23);
		$ideas->setSort("id", collection::SORT_DESC);

		// If the user is filtering add the search query to the SQL object.
		if(!empty($search)){
			$ideas->setQuery(array("", "title", "LIKE", "%" . $search . "%"));
			$operator = "AND";
		} else {
			$operator = "";
		}
		
		if($category != 0) $ideas->setQuery(array($operator, "category_id", "=", $category));
		
		$o = new view();
		
		$ideaArray = $ideas->get();
		
		// Check if there are no results
		if(count($ideaArray) == 0){
			$o = new view('frag.noresults');
			$this->viewport()->replace("recentIdeas", $o);
			return;
		}

		
		foreach($ideaArray as $idea) {
			if($idea->getHidden() && !$this->m_user->getIsAdmin()) continue;
			$template = new resourceView($idea, $this->m_user);
			$o->append( $template->get() );
		}
		
		$this->viewport()->replace("recentIdeas", $o);
	
		if($this->m_user->getIsAdmin()) $this->superview()->replace("additional-assets", util::newScript("/presentation/scripts/admin.js"));
	
	}
	
	protected function renderIdea(){
		$id = end($this->context());
		
		// Pull out the idea from the database.
		$this->m_currentIdea = new idea($id);
		
		if($this->m_currentIdea->getHidden() && !$this->m_user->getIsAdmin()){
			$this->setViewport(new view("denied"));
			return;
		}
		
		if(isset($_SESSION['createdNewIdea']) && $_SESSION['createdNewIdea'] == true){
			$new = new view('frag.createdNew');
			$new->append(new view('ideaOverview'));
			$this->setViewport($new);
			
			unset($_SESSION['createdNewIdea']);
		} else {
			$this->setViewport(new view("ideaOverview"));
		}
		
		// Information on this idea
		$this->viewport()->replace("title", $this->m_currentIdea->getTitle());
		$this->viewport()->replace("image", $this->m_currentIdea->getImage());
		$this->viewport()->replace("description", $this->m_currentIdea->getDescription());
		$this->viewport()->replace("id", $id);
		
		// Votes
		$this->viewport()->replace("voteCount", $this->m_currentIdea->countVotes());
		
		// Has the user voted
		if($this->m_currentIdea->hasVoted($this->m_user)){
			// Button to nullify the vote.
			$this->viewport()->replace("vote", "/presentation/images/minus-white.png");
		} else {
			$this->viewport()->replace("vote", "/presentation/images/thumb-up.png");
		}
		
		
		// Linked projects
		try {
			$projects = $this->m_currentIdea->getProjects();
		
			$o = new view();
			
			$frag = new view("frag.idea-project");
			
			foreach($projects as $project){
				$frag->replace("projectName", $project->getName());
				switch($project->getIncubated()){
					case 0:
						$frag->replace("projectURL", "project/" . $project->getId());
					break;
					
					case 1:
						$frag->replace("projectURL", "incubator/" . $project->getId());
					break;
				}
				$o->append( $frag->get() );
				$frag->reset();
			}
			
			$this->viewport()->replace("linked-projects", $o);
		} catch(Exception $e) {
			$this->viewport()->replace("linked-projects", "");
		}
		
		// Deal with tags.
		$tags = $this->m_currentIdea->parseTags($this->m_currentIdea);
		
		if($tags == "") $tags = "None";
		
		$this->viewport()->replace("tags", $tags);
		
		// Owner information
		$this->m_ideaOwner = $this->m_currentIdea->getOwner();
		
		if($this->m_ideaOwner->getId() == $this->m_user->getId()){
			// Currently logged in user is the owner of this idea
			$this->m_owner = true;
			$this->viewport()->replace('owner', "you");
		} else {
			$this->viewport()->replace('owner', $this->m_ideaOwner->getHTMLName());		
		}

		$this->viewport()->replace('ownerPic', $this->m_ideaOwner->getPicture());

		// Get the category
		$this->viewport()->replace('category', $this->m_currentIdea->getCategory()->getName());
		$this->viewport()->replace('cat-id', $this->m_currentIdea->getCategory()->getId());

		$button = new view();
		$button->set("<a href=\"$id/{url}\" class=\"flatButton\">{text}</a>");

		$buttons = array();
		
		if($this->m_owner) $buttons['Manage'] = "admin";
		
		// Show incubate button if user is logged in.
		if($this->m_user->getId() != null) $buttons['Incubate!'] = "incubate";

		// Setup admin options if required.
		$o = new view();
		
		foreach($buttons as $text => $url){
			$button->replace("url", $url);
			$button->replace("text", $text);
			
			$o->append($button);
			
			$button->reset();
		}
		
		$c = new view();
		
		if($this->m_user->getId() != null) $c->append(new view('frag.newComment'));
		
		// Get comments.
		$commentCollection = new collection(collection::TYPE_COMMENT);
		$commentCollection->setQuery(array("", "idea_id", "=", $id));
		$commentCollection->setSort("id", collection::SORT_DESC);
		
		foreach($commentCollection->get() as $comment){
			$c->append($comment->get($this->m_user));
		}
		
		$c->replace('picture', $this->m_user->getPicture());
		
		
		$this->viewport()->replace('comment-block', $c);
		
		$this->viewport()->replace('extraButtons', $o);
		
		$side = new view('ideaLinks');
		$side->append(new view('frag.sideInfo'));
		
		$this->superview()->replace("sideContent", $side);
		
		$assets = util::newScript("/presentation/scripts/idea.js");
		$assets .= util::newScript("/presentation/scripts/comments.js");
		
		$this->superview()->replace("additional-assets", $assets);

	}
	
	protected function comment($args){
		$this->m_noRender = true;
		
		$id = $args['id'];
		
		try {
			if($this->m_user->getId() != null) {
				$comment = new comment();
				
				$comment->setIdeaId($id);
				$comment->setBody($_POST['body']);
				$comment->setAuthor($this->m_user);
				$comment->setDate(new DateTime(null, new DateTimeZone('UTC')));
				
				$comment_id = $comment->commit();
				
				$html = $comment->get($this->m_user);
				
				// Fire off a notification
				
				$notification = new notification();
				$action = array(
					"user" => $this->m_user->getName(),
					"body" => $_POST['body'],
					"action" => str_replace(array("{tmpl}", "{type}"), array(util::id(new idea($id))->getTitle(), "idea"), notification::NOTIFICATION_COMMENT),
					"url" => str_replace("/comment", "", $this->getUrl()));
				$notification->compose(new view('mail'), $action);
				$notification->send();
				
				echo json_encode(array("status" => 200, "html" => $html));
				
			} else {
				echo json_encode(array("status" => 599, "message" => "You must be signed in to comment on this idea"));
			}
		
		} catch(Exception $e){
			echo json_encode(array("status" => 599, "message" => $e->getMessage()));
		}
	}
	
	protected function deleteComment($args){
		$this->m_noRender = true;
		
		$comment = new comment((int)$args['comment_id']);
		
		if($this->m_user->canDelete($comment)){
			$this->m_user->delete($comment);
			echo json_encode(array("status" => 200));
		} else {
			echo json_encode(array("status" => 599, "message" => "You do not have permission to delete this comment"));
		}
	}
	
	protected function vote(){
		// Should be called via AJAX.
		$this->m_noRender = true;
		
		if($this->m_user->getId() == null) {
			echo json_encode(array("status" => 500, "message" => "You must be signed in to vote for this idea"));
			exit;
		}
		
		try {
			$idea = new idea($_POST['idea']);
			
			// Has the user voted?
			if($idea->hasVoted($this->m_user)){
				// Vote down	
				$idea->voteClear($this->m_user);
				$recalc = -1;
				
				$image = "/presentation/images/thumb-up.png";
			} else {
				// Vote up
				$idea->voteUp($this->m_user);
				$recalc = 1;
				$image = "/presentation/images/minus-white.png";
			}
			
			$return = array("status" => 200, "recalc" => $recalc, "image" => $image);
		
		} catch(Exception $e){
			$return = array("status" => 500, "details" => $e->getMessage());
		}
		
		echo json_encode($return);
	}
	
	protected function newIdea(){
		// Called once the user has submitted the 'got a new idea?' box
		
		if($this->m_user->getId() == null) header("Location: /home?e=34");
		
		$this->setViewport(new view("newIdea"));
		
		$this->superview()->replace("sideContent", new view('ideaLinks'));
		
		$this->viewport()->replace("categories", util::getCategories());
		
		$this->viewport()->replace("overview", $_POST['idea']);
		
		$this->superview()->replace("additional-assets", util::newScript("/presentation/scripts/ideaAdmin.js"));
		
	}
	
	protected function ideaAdmin($args){
		$this->setViewPort(new view('ideaAdmin'));
		
		$id = (int)$args['id'];
		
		$idea = new idea($id);
		
		// Check permissions.
		if($idea->getOwner()->getId() != $this->m_user->getId()) throw new Exception("You do not have access to modify this idea.");
		
		// Make view replacements.
		$this->viewport()->replaceAll(array(
			"idea" => $idea->getTitle(),
			"owner" => $idea->getOwner()->getName(),
			"ownerPic" => $idea->getOwner()->getPicture(),
			"members" => "", // temporary
			"description" => $idea->getDescription(),
			"overview" => $idea->getOverview(),
			"chars" => strlen($idea->getOverview()),
			"image" => $idea->getImage(),
			"voteCount" => $idea->countVotes()
		));
		
		$this->viewport()->replace("categories", util::getCategories($idea->getCategory()));
		
		// Parse tags
		$t = new view();
		$t->set("{tag} ");
		$tags = $idea->parseTags($idea, $t);
		$this->viewport()->replace("tags", $tags);
		
		$this->superview()->replace("sideContent", new view('ideaLinks'));
		
		$scripts = util::addScripts(array("/presentation/lib/ckeditor/ckeditor.js", "/presentation/lib/ckeditor/adapters/jquery.js", "/presentation/scripts/ideaAdmin.js"));
		
		$this->superview()->replace("additional-assets", $scripts);
	}
	
	protected function adminSave($args){
		$id = $args['id'];
		
		try {
			$idea = new idea($id);
			
			$idea->setImage($_FILES['image']);
			$idea->setDescription($_POST['description']);
			$idea->setOverview($_POST['overview']);
			$idea->setCategory(new category((int)$_POST['category']));

			$idea->commit();
			
			$idea->addTags($idea, $_POST['tags']);
		
			$this->redirect("/idea/" . $id);
		
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	protected function incubateIdea($args){
		$this->setViewPort(new view('incubate'));
		
		$id = (int)$args['id'];
		
		$idea = new idea($id);
		
		$this->viewport()->replaceAll(array(
			"idea" => $idea->getTitle(),
			"id" => $id,
			"categories" => util::getCategories($idea->getCategory())
		));
		
		$this->superview()->replace("sideContent", new view('ideaLinks'));
	}
	
	protected function processIncubation($args){
		$idea_id = (int)$_POST['id'];
		
		try {
			$idea = new idea($idea_id);
		
			$project = new project();
			
			$project->setName($_POST['name']);
			$project->setOverview($_POST['overview']);
			$project->setDescription($_POST['description']);
			$project->setIncubated(1);
			$project->setCategory(new category((int)$_POST['category']));
			
			$project_id = $project->commit();

			$project->setImage($_FILES['image']);
			
			$project->commit();

			$project->addMember($this->m_user, project::ROLE_ADMIN);

			$project->setIdea($idea);
			
			$project->addTags($project, $_POST['tags']);
			
			// Send a notification
			$notification = new notification();
			$action = array(
				"user" => $this->m_user->getName(),
				"body" => $_POST['overview'],
				"action" => str_replace(array("{idea}", "{project}"), array($idea->getTitle(), $_POST['name']), notification::NOTIFICATION_INCUBATED),
				"url" => str_replace("/incubate/confirm", "", $this->getUrl()));
			$notification->compose(new view('mail'), $action);
			$notification->send();
			
			$this->redirect("/incubator/" . $project_id);

		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	protected function addIdea(){
		if($this->m_user->getId() == null) header("Location: /home?e=34");
		
		try {
			
			$idea = new idea();
			
			$idea->setTitle($_POST['ideaTitle']);
			$idea->setOverview($_POST['overview']);
			$idea->setDescription($_POST['description']);
			$idea->setOwner($this->m_user);
			$idea->setCategory(new category((int)$_POST['category']));
			
			$id = $idea->commit();
			
			// Parse tags
			$idea->addTags($idea, $_POST['tags']);
			
			$_SESSION['createdNewIdea'] = true;
			
			// Send a notification
			$notification = new notification();
			$action = array(
				"user" => $this->m_user->getName(),
				"body" => $_POST['overview'],
				"action" => str_replace(array("{tmpl}"), array($idea->getTitle()), notification::NOTIFICATION_IDEA),
				"url" => str_replace("new/add", $id, $this->getUrl()));
			$notification->compose(new view('mail'), $action);
			$notification->send();
			
			$this->redirect("/idea/" . $id);
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	protected function noRender(){
		return $this->m_noRender;
	}
}
