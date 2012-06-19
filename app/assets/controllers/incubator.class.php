<?
class controller_incubator extends controller {
	
	private $m_user;
	private $m_noRender = false;
	private $m_currentProject;
	private $m_projectIdea;
	
	private $m_pageLimit = 9;

	public function renderViewport() {
		$this->m_user = $this->objects("user");

		// Select the tab
		util::selectTab($this->superview(), "incubator");

		util::userBox($this->m_user, $this->superView());

		$this->bind("^[0-9]+$", "renderItem");

		$this->bind("^(?P<id>[0-9]+)/comment$", "comment"); // Comment on an idea 
		$this->bind("^(?P<id>[0-9]+)/comment/(?P<comment_id>[0-9]+)/delete", "deleteComment"); // Delete comment

		$this->bind("^(?P<id>[0-9]+)/vote$", "vote"); // DATA - vote on an idea

		$this->bind("^(?P<id>[0-9]+)/admin$", "renderAdmin");
		$this->bind("^(?P<id>[0-9]+)/admin/update$", "adminSave");
		$this->bind("^(?P<id>[0-9]+)/admin/promote$", "adminPromote");
		$this->bind("^(?P<id>[0-9]+)/admin/addMembers$", "addMembers");
		$this->bind("^(?P<id>[0-9]+)/admin/demoteMember$", "demoteMember");

		// Bind pages
		$this->bind("^page/(?P<id>[0-9]+)", "incubatorIndex");

		$this->bindDefault('incubatorIndex');
	}

	protected function incubatorIndex($args = NULL){
		$this->setViewport(new view("incubatorIndex"));

		// Get the pageID, otherwise set to 1.
		$pageId = isset($args['id']) ? (int)$args['id'] : 1;
		
		// We need to start at 0 in the database, really.
		$pageId--;

		$this->pageName = "- Incubator";

		$search = isset($_GET['search']) ? $_GET['search'] : "";
		$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

		$projects = new collection(collection::TYPE_INCUBATED);
		$projects->setLimit($pageId * $this->m_pageLimit, $this->m_pageLimit);
		$projects->setSort("id", collection::SORT_DESC);

		// If the user is filtering add the search query to the SQL object.
		if(!empty($search)){
			$projects->setQuery(array("AND", "name", "LIKE", "%" . $search . "%"));
		}
		
		if($category != 0) $projects->setQuery(array("AND", "category_id", "=", $category));
		
		if(!$this->m_user->getIsAdmin()) $projects->setQuery(array("AND", "hidden", "=", 0));
		
		$render = new view();
		
		foreach($projects->get() as $project) {
			if($project->getHidden() && !$this->m_user->getIsAdmin()) continue;
			
			$template = new resourceView($project, $this->m_user);
			
			$render->append($template->get());
		}

		$this->viewport()->replace("recentideas", $render);
		
		$side = new view('frag.filters');
		$side->append(new view('ideaLinks'));
		$side->append(new view("frag.projectResources"));
		$side->append(new view('frag.sideInfo'));
		
		$side->replace("categories", util::getCategories());
		
		$this->superview()->replace("sideContent", $side);
		
		if($this->m_user->getIsAdmin()) $this->superview()->replace("additional-assets", util::newScript("/presentation/scripts/admin.js"));
		
		// Pagination
		$pagination = new paginationView($projects, $pageId, $this->m_pageLimit);
		$this->viewport()->replace('pages', $pagination);
	}
	
	protected function renderItem(){
		$id = end($this->context());
		
		// Pull out the idea from the database.
		$this->m_currentProject = new project($id);

		$this->pageName = "- " . $this->m_currentProject->getName();
		
		if($this->m_currentProject->getHidden() && !$this->m_user->getIsAdmin()){
			$this->setViewport(new view("denied"));
			return;
		}
		
		if($this->m_currentProject->getIncubated() == false){
			$this->redirect("/project/" . $id);
			return;
		}
		
		$this->m_projectIdea = $this->m_currentProject->getIdea();
		
		$this->setViewport(new view("incubatedOverview"));
		
		$this->viewport()->replace("title", $this->m_currentProject->getName());
		$this->viewport()->replace("image", $this->m_currentProject->getImage());
		$this->viewport()->replace("description", $this->m_currentProject->getDescription());
		$this->viewport()->replace("id", $id);
		
		$this->checkGithub();
		
		$this->renderProjectInformation();

		$this->viewport()->replace('percentage', $this->m_currentProject->getPromotionPercentage());
		
		$this->viewport()->replace("ideaName", $this->m_projectIdea->getTitle());
		$this->viewport()->replace("ideaID", $this->m_projectIdea->getId());

		// Get the category
		$this->viewport()->replace('category', $this->m_currentProject->getCategory()->getName());
		$this->viewport()->replace('cat-id', $this->m_projectIdea->getCategory()->getId());
		
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
		
		$this->viewport()->replace("tags", $tags);
		
		// Comments
		
		$c = new view();
		
		if($this->m_user->getId() != null) {
			$c->append(new view('frag.newComment'));
		} else {
			// User is not logged in
			$c->append(new view('frag.needLogin'));
		}
		
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
		
		if(!$this->m_user->getEnrollment($this->m_currentProject, resource::MEMBERSHIP_ADMIN) && $this->m_user->getId() != null){
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
		
		$this->superview()->replace("sideContent", $sidebar );
		
		
		$assets = util::newScript("/presentation/scripts/comments.js");
		$assets .= util::newScript("/presentation/scripts/project.js");
		
		$this->superview()->replace("additional-assets", $assets);
	}
	
	private function checkGithub(){
		
		$repo = $this->m_currentProject->getRepoUrl();
		
		if(strstr($repo, "github.com")){
			
			// Check to see if we have a cached version
			$objectCache = new cache("github-" . $this->m_currentProject->getId(), cache::REQUEST_DATA, 10);
			if($objectCache->has()){
				$repo = $objectCache->get();
			} else {
			
				preg_match("/github.com\/([^\/]+)\/([^\/]+)/i", $repo, $matches);
				if(count($matches) < 2) return;
				
				// Get github information
				$github = new Github_Client();
				$repo = $github->getRepoApi()->show($matches[1], $matches[2]);
				
				$objectCache->put($repo);
			}
			
			$updated_time = new DateTime($repo['pushed_at'], new DateTimeZone('Europe/London'));
			$repo['lastUpdated'] = $updated_time->diff(new DateTime())->format("%d days, %h hours and %i minutes ago");
			
			// Push information to the viewport
			$this->viewport()->replace("remote-project", util::id(new view('frag.github'))->replaceAll($repo));
		} else {
			$this->viewport()->replace("remote-project", "");
			return;
		}
	}
	
	private function renderProjectInformation(){
		
		// Set up project information
		if($this->m_currentProject->getUrl() == ""){
			$this->viewport()->replace("#projWebsite", "");
		} else {
			$this->viewport()->replace("website", $this->m_currentProject->getUrl());
		}
		
		if($this->m_currentProject->getCommunityUrl() == ""){
			$this->viewport()->replace("#communityWebsite", "");
		} else {
			$this->viewport()->replace("community-website", $this->m_currentProject->getCommunityUrl());
		}

		if($this->m_currentProject->getLicense()->getUrl() == ""){
			$this->viewport()->replace("#licenceSelection", "");
		} else {
			$this->viewport()->replace("licence", $this->m_currentProject->getLicense()->getName());
			$this->viewport()->replace("license-url", $this->m_currentProject->getLicense()->getUrl());		
		}
		
		if($this->m_currentProject->getScmUrl() == ""){
			$this->viewport()->replace("#scmWebsite", "");
		} else {
			$this->viewport()->replace("scm-url", $this->m_currentProject->getScmUrl());
		}

		if($this->m_currentProject->getRepoUrl() == ""){
			$this->viewport()->replace("#repositoryWebsite", "");
		} else {
			$this->viewport()->replace("repo-url", $this->m_currentProject->getRepoUrl());
		}
		
		
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
				
				// Fire off a notification
				
				$notification = new notification();
				$action = array(
					"user" => $this->m_user->getName(),
					"body" => $_POST['body'],
					"action" => str_replace(array("{tmpl}", "{type}"), array(util::id(new project($id))->getName(), "incubated project"), notification::NOTIFICATION_COMMENT),
					"url" => str_replace("/comment", "", $this->getUrl()));
				$notification->compose(new view('mail'), $action);
				$notification->setTitle("Comment left on " . util::id(new project($id))->getName() . " incubated project on Project REALISE");
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
	
	protected function renderAdmin($args){
		$id = $args['id'];
		
		try {
		
			$this->m_currentProject = new project((int)$id);
			
			// Does the user have access to this incubated project?			
			if( !$this->m_user->getEnrollment($this->m_currentProject, resource::MEMBERSHIP_ADMIN) ){
				$this->redirect("/incubator/" . $id);
				return;			
			}
			
			// Incubated projects and projects share the same idspace, so if this is a full project redirect to the project page.
			if($this->m_currentProject->getIncubated() == false){
				$this->redirect("/project/" . $id);
				return;
			}
			
			$this->setViewport(new view('incubatorAdmin'));
	
			$this->viewport()->replace("title", $this->m_currentProject->getName());
			$this->viewport()->replace("image", $this->m_currentProject->getImage());
			$this->viewport()->replace("description", $this->m_currentProject->getDescription());
			$this->viewport()->replace("overview", $this->m_currentProject->getOverview());
			$this->viewport()->replace("id", $id);
			$this->viewport()->replace('url', $this->m_currentProject->getUrl());
			$this->viewport()->replace('community-url', $this->m_currentProject->getCommunityUrl());
			$this->viewport()->replace('scm-url', $this->m_currentProject->getScmUrl());
			$this->viewport()->replace('repo-url', $this->m_currentProject->getRepoUrl());
			$this->viewport()->replace('percentage', $this->m_currentProject->getPromotionPercentage());
			$this->viewport()->replace("chars", strlen($this->m_currentProject->getOverview()));
	
			$this->viewport()->replace('licenses', util::getLicense($this->m_currentProject->getLicense()));
	
			$this->pageName = "- " . $this->m_currentProject->getName();
	
	
			$t = new view();
			$t->set("{tag} ");
			
			$tags = $this->m_currentProject->parseTags($this->m_currentProject, $t);
			$this->viewport()->replace("tags", $tags);
			
			$side = new view();
			
			$side->append( $this->m_currentProject->formatProjectUsers() );
			
			$side->append(new view('frag.addMembers'));
			
			// Get list of followers
			$followers = $this->m_currentProject->getMembers(project::ROLE_USER);
			
			if(count($followers) > 0){
				$follower_template = new view('frag.addFollower');
				$output = new view();
				
				foreach($followers as $follower){
					$follower_template->replaceAll(array(
						"picture" => $follower->getPicture(),
						"author" => $follower->getName(),
						"id" => $follower->getId()
					));
					
					$output->append($follower_template);
					$follower_template->reset();
				}
				
				$side->replace("followers", $output);
			
			} else {
				$side->replace("followers", "");
			}
			
			$side->append(new view('frag.projectResources'));
			
			$this->superview()->replace("sideContent",  $side);

			if($this->m_currentProject->getPromotionPercentage() > SYS_OPENNESS_THRESHOLD){
				$this->viewport()->replace('promotedisabled', '');
			} else {
				$this->viewport()->replace('promotedisabled', ' disabled="disabled"');
			}

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
			
			$this->m_currentProject->setImage($_FILES['image']);

			$this->m_currentProject->addTags($this->m_currentProject, $_POST['tags']);
			
			if((int)$_POST['licence'] != 0) $this->m_currentProject->setLicense(new license((int)$_POST['licence']));

			$this->m_currentProject->commit();
			
			$this->redirect("/incubator/" . $id);
		
		} catch(Exception $e){
			$v = new view();
			$v->set($e->getMessage);
			$this->setViewport($v);
		}
		
		
	}
	
	protected function adminPromote($args){
	
		$id = $args['id'];
		
		$this->m_currentProject = new project((int)$id);
		
		try {
			if($this->m_currentProject->getPromotionPercentage() >= SYS_OPENNESS_THRESHOLD){
			
				$this->m_currentProject->setIncubated(0);
				$this->m_currentProject->commit();
			
			
				// Send a notification
				$notification = new notification();
				$action = array(
					"user" => $this->m_user->getName(),
					"body" => $_POST['overview'],
					"action" => str_replace(array("{tmpl}"), array($this->m_currentProject->getName()), notification::NOTIFICATION_PROJECT),
					"url" => "/project/$id");
				$notification->compose(new view('mail'), $action);
				$notification->send();
			
			
				$this->redirect("/project/" . $id);
			} else {
				$this->redirect("/incubator/" . $id);
			}
		
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
