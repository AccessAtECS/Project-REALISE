<?php

class controller_opennessrating extends controller {

	private $m_user;
	private $m_currentProject;
	
	public function renderViewport() {
		
		$this->m_user = $this->objects("user");
		
		// Select the tab
		util::selectTab($this->superview(), "");	

		util::userBox($this->m_user, $this->superView());
		
		$this->superview()->replace("sideContent", util::displayOpennessSections());
		
		$this->bind('info', 'opennessInfo');
		$this->bind('legal', 'opennessLegal');
		$this->bind('standards', 'opennessStandards');
		$this->bind('knowledge', 'opennessKnowledge');
		$this->bind('governance', 'opennessGovernance');
		$this->bind('market', 'opennessMarket');
		$this->bind('completed', 'opennessEnd');
		
		$this->bind('submitInfo', 'infoProcess');
		$this->bind('submitLegal', 'legalProcess');
		$this->bind('submitStandards', 'standardsProcess');
		$this->bind('submitKnowledge', 'knowledgeProcess');
		$this->bind('submitGovernance', 'governanceProcess');
		$this->bind('submitMarket', 'marketProcess');
		
		$this->bind('resetOpenness', 'resetOpenness');
		
		$this->bindDefault('opennessIndex');
	}
		
	protected function opennessIndex(){		
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$this->pageName = "- Openness Rating";
		$this->setViewport(new view("openness"));		
		$this->viewport()->replace("id", $id);
	}
	
	protected function opennessInfo(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$pageName = "Openness Rating - Project Information";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-info"));
		$this->viewport()->replace("project_id", $id);	
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "info");
		$this->viewport()->replace("questions", $q);
	}
	
	protected function opennessLegal(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$pageName = "Openness Rating - Legal";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-legal"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "legal");
		$this->viewport()->replace("questions", $q);
	}	
	
	protected function opennessStandards(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$pageName = "Openness Rating - Data Formats and Standards";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-standards"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "standards");
		$this->viewport()->replace("questions", $q);
	}	
	
	protected function opennessKnowledge(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$pageName = "Openness Rating - Knowledge";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-knowledge"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "knowledge");
		$this->viewport()->replace("questions", $q);
	}
	
	protected function opennessGovernance(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$pageName = "Openness Rating - Governance";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-governance"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "governance");
		$this->viewport()->replace("questions", $q);
	}
	
	protected function opennessMarket(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$pageName = "Openness Rating - Market";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-market"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "market");
		$this->viewport()->replace("questions", $q);
	}
	
	protected function opennessEnd(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$pageName = "Openness Rating - Complete";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-end"));
		$this->viewport()->replace("id", $id);

		$colour = $OR->ratingColour($openness);
		$this->viewport()->replace("openness-rating", $openness);
		$this->viewport()->replace("alert-colour", $colour);
	}
		
	protected function infoProcess(){
		$OR = new opennessRating(); 
		$redirectURL = $OR->process("info", "legal");
		$this->redirect($redirectURL);
	}

	protected function legalProcess(){
		$OR = new opennessRating(); 
		$redirectURL = $OR->process("legal", "standards");
		$this->redirect($redirectURL);
	}
	
	protected function standardsProcess(){
		$OR = new opennessRating(); 
		$redirectURL = $OR->process("standards", "knowledge");
		$this->redirect($redirectURL);
	}
	
	protected function knowledgeProcess(){
		$OR = new opennessRating(); 
		$redirectURL = $OR->process("knowledge", "governance");
		$this->redirect($redirectURL);
	}
	
	protected function governanceProcess(){
		$OR = new opennessRating(); 
		$redirectURL = $OR->process("governance", "market");
		$this->redirect($redirectURL);
	}
	
	protected function marketProcess(){
		$OR = new opennessRating(); 
		$redirectURL = $OR->process("market", "end");
		$this->redirect($redirectURL);
	}
	
	protected function resetOpenness(){
		$OR = new opennessRating();
		$id = $OR->getProjectId();
		$this->auth($id);
		$OR->testProjectId($id);
		
		$deleted = $this->deleteOpenness($id);
		
		$openness = "0%";
		$this->superview()->replace("sidecontent_pageid", $id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
			
		$pageName = "Openness Rating Deleted";
			
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-deleted"));
		$this->viewport()->replace("id", $id);
	
		$colour = $OR->ratingColour($openness);
		$this->viewport()->replace("openness-rating", $openness);
		$this->viewport()->replace("alert-colour", $colour);
	}
	
	private function auth($id){
		$this->m_currentProject = new project((int)$id);
		
		if( !$this->m_user->getEnrollment($this->m_currentProject, resource::MEMBERSHIP_ADMIN) ){
			echo("You do not have permission to access the openness rating of this project");
			exit;
		}
	}
	
	private function deleteOpenness($id){
		$this->db = db::singleton();

		try{
			$this->db->single("DELETE FROM open_project_has_answer WHERE project_id = ".$id);
			$this->db->single("UPDATE project SET openness_rating = 0 WHERE id =". $id);
		} catch (Exception $e){
			echo "It was not possible to perform this action. Please try again later.";
			exit;
		}
	}
}

?>