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
		
	}
	
	protected function reportInfo(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$pageName = "Openness Rating - Project Information - Report For Project 29";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-info"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->createQuestions($id, "info", TRUE);
	}
	
	protected function reportLegal(){
		
	}
	
	protected function reportStandards(){
		
	}
	
	protected function reportKnowledge(){
		
	}
	
	protected function reportGovernance(){
		
	}
	
	protected function reportMarket(){
		
	}

}
?>