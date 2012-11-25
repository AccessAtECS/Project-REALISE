<?php

class controller_opennessreport extends controller {

	private $m_user;

	public function renderViewport() {
		
		$this->m_user = $this->objects("user");
		
		// Select the tab	
		util::selectTab($this->superview(), "");	

		util::userBox($this->m_user, $this->superView());
		
		$this->superview()->replace("sideContent", util::displayOpennessReportSections());
		
		$this->bind('report', 'reportIndex');
		$this->bind('info', 'reportInfo');
		$this->bind('legal', 'reportLegal');
		$this->bind('standards', 'reportStandards');
		$this->bind('knowledge', 'reportKnowledge');
		$this->bind('governance', 'reportGovernance');
		$this->bind('market', 'reportMarket');
		
		$this->bindDefault('reportIndex');
	}

	protected function reportIndex(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$OR = new opennessRating();
		
		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
				
		$this->pageName = "- Openness Rating";
		$pageName = "Project Information - Openness Report For Project ".$id;
		$this->setViewport(new view("openness-report"));
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->replace("page-name", $id);
		$this->viewport()->replace("id", $id);
	}
	
	protected function reportInfo(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$OR = new opennessRating();
		$report = new opennessReport();
		
		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
				
		$pageName = "Project Information - Openness Report For Project ".$id;

		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-report-pages"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "info", TRUE);
		$this->viewport()->replace("questions", $q);
		$button = $report->button($id, "legal");
		$this->viewport()->replace("next-button", $button);
	}

	protected function reportLegal(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$OR = new opennessRating();
		$report = new opennessReport();
		
		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
				
		$pageName = "Legal - Openness Report For Project ".$id;
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-report-pages"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "legal", TRUE);
		$this->viewport()->replace("questions", $q);
		$button = $report->button($id, "standards");
		$this->viewport()->replace("next-button", $button);
	}
	
	protected function reportStandards(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$OR = new opennessRating();
		$report = new opennessReport();
		
		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
				
		$pageName = "Legal - Openness Report For Project ".$id;

		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-report-pages"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "standards", TRUE);
		$this->viewport()->replace("questions", $q);
		$button = $report->button($id, "knowledge");
		$this->viewport()->replace("next-button", $button);
	}
	
	protected function reportKnowledge(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$OR = new opennessRating();
		$report = new opennessReport();
		
		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
				
		$pageName = "Legal - Openness Report For Project ".$id;

		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-report-pages"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "knowledge", TRUE);
		$this->viewport()->replace("questions", $q);
		$button = $report->button($id, "governance");
		$this->viewport()->replace("next-button", $button);
	}
	
	protected function reportGovernance(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$OR = new opennessRating();
		$report = new opennessReport();
		
		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
				
		$pageName = "Legal - Openness Report For Project ".$id;

		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-report-pages"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "governance", TRUE);
		$this->viewport()->replace("questions", $q);
		$button = $report->button($id, "market");
		$this->viewport()->replace("next-button", $button);
	}
	
	protected function reportMarket(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$OR = new opennessRating();
		$report = new opennessReport();
		
		$id = $OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
				
		$pageName = "Legal - Openness Report For Project ".$id;

		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-report-pages"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($OR->helpJS());
		$q = $OR->createQuestions($id, "market", TRUE);
		$this->viewport()->replace("questions", $q);
		$button = $report->button($id, "end");
		$this->viewport()->replace("next-button", $button);
	}

}
?>