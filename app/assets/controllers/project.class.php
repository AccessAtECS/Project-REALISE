<?
class controller_project extends controller {
	
	private $m_user;
	private $m_inStep;
	private $m_noRender = false;
	private $m_currentProject;
	
	public function renderViewport() {
		$this->m_user = $this->objects("user");

		// Select the tab	
		util::selectTab($this->superview(), "project");	

		util::userBox($this->m_user, $this->superView());		

		$this->bind("[0-9]+$", "renderItem");
		
		$this->bind("(?P<id>[0-9]+)/comment$", "comment"); // Comment on an idea
		$this->bind("(?P<id>[0-9]+)/comment/(?P<comment_id>[0-9]+)/delete", "deleteComment"); // Delete comment
		
		$this->bind("(?P<id>[0-9]+)/vote$", "vote"); // DATA - vote on an idea
		
		$this->bind("(?P<id>[0-9]+)/admin$", "itemAdmin");
		$this->bind("(?P<id>[0-9]+)/admin/update$", "adminSave");
		$this->bind("(?P<id>[0-9]+)/admin/addMembers$", "addMembers");
		$this->bind("(?P<id>[0-9]+)/admin/demoteMember$", "demoteMember");

		$this->bindDefault('projectIndex');
	}
	
	protected function projectIndex(){
		$this->setViewport(new view("incubatorIndex"));

		$search = isset($_GET['search']) ? $_GET['search'] : "";
		$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

		$side = new view('frag.filters');
		$side->append(new view('ideaLinks'));
		$side->append(new view("frag.projectResources"));
		$side->append(new view('frag.sideInfo'));

		$side->replace("categories", util::getCategories());
	
		$this->superview()->replace("sideContent", $side);	

		$projects = new collection(collection::TYPE_PROJECT);
		$projects->setLimit(24);
		$projects->setSort("id", collection::SORT_DESC);
		
		// If the user is filtering add the search query to the SQL object.
		if(!empty($search)){
			$projects->setQuery(array("AND", "name", "LIKE", "%" . $search . "%"));
		}
		
		if($category != 0) $projects->setQuery(array("AND", "category_id", "=", $category));
				
		
		$o = new view();
		$template = new view("frag.idea");
		
		$delete = new view('frag.deleteComment');
		
		foreach($projects->get() as $project) {
			if($project->getHidden() && !$this->m_user->getIsAdmin()) continue;
			$idea = $project->getIdea();
			
			$template->replace("title", $project->getName());
			$template->replace("url", "project/" . $project->getId());
			$template->replace("pitch", $project->getOverview());
			$template->replace("image", $project->getImage());
			$template->replace("id", $project->getId());
			$template->replace("type", "project");
			$template->replace("points", $project->countVotes());
			
			if($this->m_user->getIsAdmin()){
				if($project->getHidden()){
					$template->replace('delete', 'HIDDEN');
				} else {
					// Display the deletion icon
					$template->replace('delete', $delete);
				}
			} else {
				$template->replace('delete', '');
			}
			
			$o->append( $template->get() );
			$template->reset();
		}
		
		$this->viewport()->replace("recentIdeas", $o);
		
		if($this->m_user->getIsAdmin()) $this->superview()->replace("additional-assets", util::newScript("/presentation/scripts/admin.js"));
	}
	
	protected function renderItem(){
		$id = end($this->context());
		
		// Pull out the idea from the database.
		$this->m_currentProject = new project($id);
		$this->m_projectIdea = $this->m_currentProject->getIdea();

		if($this->m_currentProject->getHidden() && !$this->m_user->getIsAdmin()){
			$this->setViewport(new view("denied"));
			return;
		}
		
		$this->setViewport(new view("projectOverview"));
		
		$this->viewport()->replace("title", $this->m_currentProject->getName());
		$this->viewport()->replace("image", $this->m_currentProject->getImage());
		$this->viewport()->replace("description", $this->m_currentProject->getDescription());
		$this->viewport()->replace("website", $this->m_currentProject->getUrl());
		$this->viewport()->replace("community-website", $this->m_currentProject->getCommunityUrl());
		$this->viewport()->replace("scm-url", $this->m_currentProject->getScmUrl());
		$this->viewport()->replace("repo-url", $this->m_currentProject->getRepoUrl());
		$this->viewport()->replace("id", $id);

		// Openness ratings
		$or = $this->m_currentProject->getOpennessRating();
		
		if($or == null){
			$this->viewport()->replace("openness-progress", "This project has no openness rating yet.");
		} else {
			$bar = new view();
			$bar->append("Openness Rating:");
			$bar->append(new view("frag.pbar"));
			$bar->replace("percentage", $or->openness);
			$this->viewport()->replace("openness-progress", $bar);
		}

		
		$this->viewport()->replace("ideaName", $this->m_projectIdea->getTitle());
		$this->viewport()->replace("ideaID", $this->m_projectIdea->getId());
		
		if( $this->m_user->getEnrollment($this->m_currentProject, resource::MEMBERSHIP_ADMIN) ){
			$buttons = array("Manage" => "{$this->m_currentProject->getId()}/admin");
			
			$this->viewport()->replace("buttons", util::flatButtons($buttons) );
			
		} else {
			$this->viewport()->replace("buttons", "" );
		}	
		
		// Get the category
		$this->viewport()->replace('category', $this->m_currentProject->getCategory()->getName());
		$this->viewport()->replace('cat-id', $this->m_currentProject->getCategory()->getId());

		// Deal with tags.
		$tags = $this->m_currentProject->parseTags($this->m_currentProject);
		
		if($tags == "") $tags = "None";
		
		$this->viewport()->replace("licence", $this->m_currentProject->getLicense()->getName());
		$this->viewport()->replace("license-url", $this->m_currentProject->getLicense()->getUrl());
		
		$this->viewport()->replace("tags", $tags);
	
		// Comments
		
		$c = new view();
		
		if($this->m_user->getId() != null) $c->append(new view('frag.newComment'));
		
		// Get comments.
		$commentCollection = new collection(collection::TYPE_COMMENT);
		$commentCollection->setQuery(array("", "project_id", "=", $id));
		$commentCollection->setSort("id", collection::SORT_DESC);
		
		foreach($commentCollection->get() as $comment){
			$c->append($comment->get($this->m_user));
		}	
		
		$c->replace('picture', $this->m_user->getPicture());
		
		$this->viewport()->replace('comment-block', $c);


		// Deal with sidebar
		
		// Get list of users
		$sidebar = new view();
		$sidebar->append( $this->m_currentProject->formatProjectUsers() );
		$sidebar->append(new view('frag.projectFollowers'));
		
		$project_followers = $this->m_currentProject->countVotes(resource::MEMBERSHIP_USER);
		
		$sidebar->replace('follower-count', $project_followers);
		
		
		if(!$this->m_user->getEnrollment($this->m_currentProject, resource::MEMBERSHIP_ADMIN)){
			$voteButton = new view('frag.followProject');
			// Has the user voted?
			if($this->m_currentProject->hasVoted($this->m_user)){
				$voteButton->replace('follow', "Unfollow");
				$voteButton->replace('vote', "/presentation/images/minus-white.png");
			} else {
				$voteButton->replace('follow', "Follow");
				$voteButton->replace('vote', "/presentation/images/plus-circle.png");
			}
			$sidebar->replace('follow', $voteButton);
		} else {
			$sidebar->replace('follow', '');
		}
		
		if($project_followers > 0){
			// Get a list of followers
			$followers = $this->m_currentProject->getVoters(resource::MEMBERSHIP_USER);
			
			$output = new view();
			$follower_template = new view('frag.follower');
			
			foreach($followers as $follower){
				$follower_template->replaceAll(array(
					"picture" => $follower->getPicture(),
					"author" => $follower->getName()
				));
				
				$output->append($follower_template);
				$follower_template->reset();
			}
			
			$sidebar->replace('followers', $output);
		} else {
			$sidebar->replace('followers', '');
		}
		
		$sidebar->append(new view("frag.projectResources"));
		
		
		$this->superview()->replace("sideContent", $sidebar );

		$assets = util::newScript("/presentation/scripts/comments.js");
		$assets .= util::newScript("/presentation/scripts/project.js");
		
		$this->superview()->replace("additional-assets", $assets);
				
	}

	protected function comment($args){
		$this->m_noRender = true;
		
		$id = $args['id'];
		
		try {
			if($this->m_user->getId() != null) {
				$comment = new comment();
				
				$comment->setProjectId($id);
				$comment->setBody($_POST['body']);
				$comment->setAuthor($this->m_user);
				$comment->setDate(new DateTime(null, new DateTimeZone('UTC')));
				
				$comment_id = $comment->commit();
				
				$html = $comment->get($this->m_user);
				
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
	
	protected function itemAdmin($args){
		$id = $args['id'];
		
		try {
		
			$this->m_currentProject = new project((int)$id);
			
			// Does the user have access to this incubated project?			
			if( !$this->m_user->getEnrollment($this->m_currentProject, resource::MEMBERSHIP_ADMIN) ){
				$this->redirect("/project/" . $id);
				return;			
			}
			
			// Incubated projects and projects share the same idspace, so if this is a full project redirect to the project page.
			if($this->m_currentProject->getIncubated()){
				$this->redirect("/incubator/" . $id);
				return;
			}
			
			$this->setViewport(new view('projectAdmin'));
	
			$this->viewport()->replace("title", $this->m_currentProject->getName());
			$this->viewport()->replace("image", $this->m_currentProject->getImage());
			$this->viewport()->replace("description", $this->m_currentProject->getDescription());
			$this->viewport()->replace("overview", $this->m_currentProject->getOverview());
			$this->viewport()->replace("id", $id);
			$this->viewport()->replace('url', $this->m_currentProject->getUrl());
			$this->viewport()->replace('community-url', $this->m_currentProject->getCommunityUrl());
			$this->viewport()->replace('scm-url', $this->m_currentProject->getScmUrl());
			$this->viewport()->replace('repo-url', $this->m_currentProject->getRepoUrl());
			$this->viewport()->replace("chars", strlen($this->m_currentProject->getOverview()));
			
			$this->viewport()->replace('licenses', util::getLicense($this->m_currentProject->getLicense()));
	
			// Openness ratings
			$or = $this->m_currentProject->getOpennessRating();
			
			if($or == null){
				$this->viewport()->replace("openness-progress", new view('frag.noRating'));
			} else {
				$bar = new view();
				$bar->append("Openness Rating:");
				$bar->append(new view("frag.pbar"));
				$bar->replace("percentage", $or->openness);
				$this->viewport()->replace("openness-progress", $bar);
			}	
	
			$t = new view();
			$t->set("{tag} ");
			
			$tags = $this->m_currentProject->parseTags($this->m_currentProject, $t);
			$this->viewport()->replace("tags", $tags);
			
			$side = new view();
			
			$admin_member = new view('frag.innovator');
			$admin_member->replace("name", "{name} <div class='delete'>" . new view('frag.deleteComment') . "</div>", view::REPLACE_CORE);
			
			$side->append( $this->m_currentProject->formatProjectUsers(resource::MEMBERSHIP_ADMIN, $admin_member) );
			
			$side->append(new view('frag.addMembers'));
			
			// Get list of followers
			$followers = $this->m_currentProject->getMembers(project::ROLE_USER);
			
			if(count($followers) > 0){
				$follower_template = new view('frag.addFollower');
				$output = new view();
				
				foreach($followers as $follower){
					
					$nameHeader = new view();
					
					$nameHeader->append($follower->getName());
					
					$follower_template->replaceAll(array(
						"picture" => $follower->getPicture(),
						"author" => $nameHeader,
						"id" => $follower->getId()
					));
					
					$output->append($follower_template);
					$follower_template->reset();
				}
				
				$side->replace("followers", $output);
			
			} else {
				$side->replace("followers", "");
			}
						
			
			$this->superview()->replace("sideContent", $side );

			$scripts = util::addScripts(array("/presentation/lib/ckeditor/ckeditor.js", "/presentation/lib/ckeditor/adapters/jquery.js", "/presentation/scripts/ideaAdmin.js"));
		
			$this->superview()->replace("additional-assets", $scripts);
			
		} catch(Exception $e){
			echo $e->getMessage();
		}	
	}
	
	protected function adminSave($args){
		$id = $args['id'];
		
		$this->m_currentProject = new project((int)$id);
		
		try {
			$this->m_currentProject->setUrl($_POST['url']);
			$this->m_currentProject->setDescription($_POST['description']);
			$this->m_currentProject->setOverview($_POST['overview']);
			$this->m_currentProject->setCommunityUrl($_POST['community']);
			$this->m_currentProject->setScmUrl($_POST['scm']);
			$this->m_currentProject->setRepoUrl($_POST['repo']);
			
			if((int)$_POST['licence'] != 0) $this->m_currentProject->setLicense(new license((int)$_POST['licence']));
			
			$this->m_currentProject->setImage($_FILES['image']);

			$this->m_currentProject->addTags($this->m_currentProject, $_POST['tags']);

			$this->m_currentProject->commit();
			
			$this->redirect("/project/" . $id);
		
		} catch(Exception $e){
			$v = new view();
			$v->set($e->getMessage);
			$this->setViewport($v);
		}
		
		
	}

	protected function addMembers($args){
		$this->m_noRender = true;
		
		if(count($_POST['users']) == 0) return;
		
		$id = $args['id'];
		
		$project = new project((int)$id);
		
		if($this->m_user->getEnrollment($project, resource::MEMBERSHIP_ADMIN)){
			foreach($_POST['users'] as $userid){
				$user = new user((int)$userid);
				
				$project->promoteUser($this->m_user, $user, resource::MEMBERSHIP_ADMIN);
				$return = array("status" => 200);
			}
		} else {
			$return = array("status" => 500, "details" => "You do not have adequate permissions to perform this function.");
		}
		
		echo json_encode($return);
	}
	
	protected function demoteMember($args){
		$this->m_noRender = true;
		
		if(empty($_POST['user_id'])) return;
		
		$id = $args['id'];
		
		$project = new project((int)$id);
		
		if($this->m_user->getEnrollment($project, resource::MEMBERSHIP_ADMIN)){
			
				$user = new user((int)$_POST['user_id']);
				
				$project->promoteUser($this->m_user, $user, resource::MEMBERSHIP_USER);
				$return = array("status" => 200);
		} else {
			$return = array("status" => 500, "details" => "You do not have adequate permissions to perform this function.");
		}
		
		echo json_encode($return);
	}

	protected function vote(){
		// Should be called via AJAX.
		$this->m_noRender = true;
		
		if($this->m_user->getId() == null) {
			echo json_encode(array("status" => 500, "message" => "You must be signed in to vote for this idea"));
			exit;
		}
		
		try {
			$project = new project((int)$_POST['project']);
			
			// Has the user voted?
			if($project->hasVoted($this->m_user)){
				// Vote down	
				$project->voteClear($this->m_user);
				$action = "unfollow";
				$recalc = -1;
				
				$image = "/presentation/images/plus-circle.png";
			} else {
				// Vote up
				$project->voteUp($this->m_user);
				$recalc = 1;
				$action = "follow";
				$image = "/presentation/images/minus-white.png";
			}
			
			$return = array("status" => 200, "recalc" => $recalc, "image" => $image, "action" => $action);
		
		} catch(Exception $e){
			$return = array("status" => 500, "details" => $e->getMessage());
		}
		
		echo json_encode($return);
	}
	
	protected function noRender(){
		return $this->m_noRender;
	}
	
}

?>