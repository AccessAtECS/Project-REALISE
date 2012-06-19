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
		$this->createQuestions($id, "info");
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
		$this->createQuestions($id, "legal");
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
		$this->createQuestions($id, "standards");
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
		$this->createQuestions($id, "knowledge");
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
		$this->createQuestions($id, "governance");
	}
	
	protected function opennessMarket(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$this->auth($id);
		$OR = new opennessRating();

		$id = $$OR->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $OR->getOpennessRating($id);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $OR->ratingColour($openness));
		
		$pageName = "Openness Rating - Market";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-market"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($$OR->helpJS());
		$this->createQuestions($id, "market");
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

		$colour = $this->ratingColour($openness);
		$this->viewport()->replace("openness-rating", $openness);
		$this->viewport()->replace("alert-colour", $colour);
	}
	
	protected function createQuestions($project_id, $section){
		$OR = new opennessRating(); 
		$questions = $OR->getQuestions($section);
		
		$q = new view();
		
		try{
			foreach($questions[0] as $question){	
			
				switch ($question['type']){
				
					case 'drop':
						$template = new view('frag.opennessQuestionDrop');
						$template->replace("question", $question['question']);
						$template->replace("sub_question", $question['sub_question']);
						$template->replace("section", $question['section']);
						$template->replace("question_id", $question['id']);
						$help = $OR->helpText($question['id'], $question['help']);
						$template->replace("info", $help);

						$answers = $OR->getAnswers($question['id']);
						$a = new view();
						
						if($question['has_dont_know_answer'] == "1"){
							$answerDN = new view('frag.opennessAnswerDrop');
							$answerDN->replace("answer", "Don't know");
							$answerDN->replace("answer_id", "dn");
							$a->append($answerDN->get());
						}
					
						foreach($answers[0] as $answer){
							$answerFrag = new view('frag.opennessAnswerDrop');
							$answerFrag->replace("answer", $answer['answer']);
							$answerFrag->replace("answer_id", $answer['id']);
							
							//has answer been selected before
							$value = $OR->viewAnswer($project_id, $question['id']);
							
							if($answer['id'] == $value[0]){
								$answerFrag->replace("select-".$value[0], "selected");
							}
							
							$a->append($answerFrag->get());
						}
						$template->replace("options", $a);						
					break;
					
					case 'text':
						foreach($questions[0] as $question){
						
							$template = new view('frag.opennessQuestionText');
							$template->replace("question", $question['question']);
							$template->replace("sub_question", $question['sub_question']);
							$template->replace("section", $question['section']);
							$template->replace("question_id", $question['id']);
							$help = $OR->helpText($question['id'], $question['help']);
							$template->replace("info", $help);

							$value = $OR->viewAnswer($project_id, $question['id']);
							
							if(empty($value)){
								$template->replace("value", "");
							}
							else {
								$template->replace("value", $value[0]);
							}							
							$q->append($template->get());
						}

						$this->viewport()->replace("questions", $q);
					break;
					
					case 'multi-select':
						$template = new view('frag.opennessQuestionMultiSelect');
						$template->replace("question", $question['question']);
						$template->replace("sub_question", $question['sub_question']);
						$template->replace("section", $question['section']);
						$template->replace("question_id", $question['id']);
						$help = $OR->helpText($question['id'], $question['help']);
						$template->replace("info", $help);

						$answers = $OR->getAnswers($question['id']);
						$a = new view();
					
						foreach($answers[0] as $answer){
							$answerFrag = new view('frag.opennessAnswerMultiSelect');
							$answerFrag->replace("answer", $answer['answer']);
							$answerFrag->replace("answer_id", $answer['id']);
							$answerFrag->replace("section", $question['section']);
							$answerFrag->replace("question_id", $question['id']);
							
							$value = $OR->viewAnswer($project_id, $question['id']);
							
							if(empty($value)){}
							else{
								if(in_array($answer['id'], $value)){
									$answerFrag->replace("checked", 'checked="yes"');
								}
							}	
							$a->append($answerFrag->get());
							
						}					
						$template->replace("options", $a);
						
					break;
					
					case 'scale':
						$i = 0;
						
						$template = new view('frag.opennessQuestionScale');
						$template->replace("question", $question['question']);
						$template->replace("sub_question", $question['sub_question']);
						$template->replace("question_id", $question['id']);
						$help = $OR->helpText($question['id'], $question['help']);
						$template->replace("info", $help);

						$answers = $OR->getAnswers($question['id']);
						$a = new view();
					
						foreach($answers[0] as $answer){
							$answerFrag = new view('frag.opennessAnswerScale');
							$answerFrag->replace("answer", $answer['answer']);
							$answerFrag->replace("answer_id", $answer['id']);
							$answerFrag->replace("section", $question['section']);
							$answerFrag->replace("number", $i);
							$answerFrag->replace("question_id", $question['id']);
							$template->replace("answer_id",$answer['id']);
							$template->replace("answer".$i,$i);
							
							$value = $OR->viewAnswer($project_id, $question['id']);
							
							if(empty($value)){}
							else{
								if(in_array($answer['id'], $value)){
									$answerFrag->replace("checked", 'checked="yes"');
								}
							}								
							
							$a->append($answerFrag->get());
							$i++;
						}
						$template->replace("answers", $a);
						$i = 0;
					break;
					
					default:
						echo "default question";
				}
				
				$q->append($template->get());
			}

			$this->viewport()->replace("questions", $q);	
			
		} catch(Exception $e){
			echo "Unable to render openness rating question list. Please try again later.";
			exit;
		}		
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
	
	private function auth($id){
		$this->m_currentProject = new project((int)$id);
		
		if( !$this->m_user->getEnrollment($this->m_currentProject, resource::MEMBERSHIP_ADMIN) ){
			echo("You do not have permission to access the openness rating of this project");
			exit;			
		}
		
		$OR = new opennessRating();
	}
}

?>