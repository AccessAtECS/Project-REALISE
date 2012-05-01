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

			$q->append($template->get());
		}

		$this->viewport()->replace("questions", $q);
	}
	
	protected function opennessLegal(){
		$this->pageName = "- Openness Rating - Legal";
		
		$this->setViewport(new view("openness-legal"));
		$questions = $this->getQuestions("legal");
		
		$q = new view();
			
		foreach($questions[0] as $question){	
		
			switch ($question['type']){
			
				case 'drop':
					$template = new view('frag.opennessQuestionDrop');
					$template->replace("question", $question['question']);
					$template->replace("sub_question", $question['sub_question']);
					$template->replace("section", $question['section']);
					$template->replace("question_id", $question['id']);

					$answers = $this->getAnswers($question['id']);
					$a = new view();
					
					foreach($answers[0] as $answer){

						$answerFrag = new view('frag.opennessAnswerDrop');
						$answerFrag->replace("answer", $answer['answer']);
						$answerFrag->replace("answer_id", $answer['id']);
						$a->append($answerFrag->get());

					}
					
					$answerFrag->replace("options", $a);
					$answerFrag->reset();
					
				break;
				
				case 'text':
					$template = new view('frag.opennessQuestionText');
					$template->replace("question", $question['question']);
					$template->replace("sub_question", $question['sub_question']);
					$template->replace("section", $question['section']);
					$template->replace("question_id", $question['id']);
				break;
				
				case 'multi-select':

				break;
				
				case 'scale':

				break;
				
				default:
					echo "";
			}
			
			$q->append($template->get());
		}

		$this->viewport()->replace("questions", $q);		
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

	protected function getAnswers($question_id){
		
		$this->db = db::singleton();
		$results = $this->db->select(array("*"), "open_answer", array(array("", "open_question_id", "=", $question_id)))->run();
		
		return $results;
	}		
	
	
	
}

?>