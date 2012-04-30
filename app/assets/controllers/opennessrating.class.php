<?php

class controller_opennessrating extends controller {

	private $m_user;

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
		
		$this->bindDefault('opennessIndex');
	}
		
	protected function opennessIndex(){		
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		
		$this->pageName = "- Openness Rating";
		
		$this->setViewport(new view("openness"));
		$this->viewport()->replace("id", $id);
		
	}
	
	protected function opennessInfo(){
		$this->pageName = "- Openness Rating - Project Information";
			
		$this->setViewport(new view("openness-info"));
		$questions = $this->getQuestions("info");
		
		$q = new view();
		
		foreach($questions[0] as $question){
		
		$template = new view('frag.opennessQuestionText');
		$template->replace("question", $question['question']);
		$template->replace("sub_question", $question['sub_question']);
		$template->replace("section", $question['section']);
		$template->replace("question_id", $question['id']);
		
		
		$q->append
	

		
		}

		$this->viewport()->replace("questions", $q);
		
	}
	
	protected function opennessLegal(){
		$this->pageName = "- Openness Rating - Legal";
		
		$this->setViewport(new view("openness-legal"));
	}	
	
	protected function opennessStandards(){
		$this->pageName = "- Openness Rating - Data Formats and Standards";
		
		$this->setViewport(new view("openness-standards"));
	}	
	
	protected function opennessKnowledge(){
		$this->pageName = "- Openness Rating - Knowledge";
		
		$this->setViewport(new view("openness-knowledge"));
	}
	
	protected function opennessGovernance(){
		$this->pageName = "- Openness Rating - Governance";
		
		$this->setViewport(new view("openness-governance"));
	}
	
	protected function opennessMarket(){
		$this->pageName = "- Openness Rating - Market";
		
		$this->setViewport(new view("openness-market"));
	}	
	
	protected function getQuestions($section){
		
		$this->db = db::singleton();
		$results = $this->db->select(array("*"), "open_question", array(array("", "section", "=", $section)))->run();
		
		return $results;
	}	
	
	
	
}

?>