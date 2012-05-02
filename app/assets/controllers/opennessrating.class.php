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
		$this->createQuestions("info");
	}
	
	protected function opennessLegal(){
		$this->pageName = "- Openness Rating - Legal";
		$this->setViewport(new view("openness-legal"));
		$this->createQuestions("legal");
	}	
	
	protected function opennessStandards(){
		$this->pageName = "- Openness Rating - Data Formats and Standards";
		$this->setViewport(new view("openness-standards"));
		$this->createQuestions("standards");
	}	
	
	protected function opennessKnowledge(){
		$this->pageName = "- Openness Rating - Knowledge";
		$this->setViewport(new view("openness-knowledge"));
		$this->createQuestions("knowledge");
	}
	
	protected function opennessGovernance(){
		$this->pageName = "- Openness Rating - Governance";
		$this->setViewport(new view("openness-governance"));
		$this->createQuestions("governance");
	}
	
	protected function opennessMarket(){
		$this->pageName = "- Openness Rating - Market";
		$this->setViewport(new view("openness-market"));
		$this->createQuestions("market");
	}	
	
	protected function createQuestions($section){
	
		$questions = $this->getQuestions($section);
		
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
					$template->replace("options", $a);
					
				break;
				
				case 'text':

					foreach($questions[0] as $question){
					
						$template = new view('frag.opennessQuestionText');
						$template->replace("question", $question['question']);
						$template->replace("sub_question", $question['sub_question']);
						$template->replace("section", $question['section']);
						$template->replace("question_id", $question['id']);

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

					$answers = $this->getAnswers($question['id']);
					$a = new view();
				
					foreach($answers[0] as $answer){
						$answerFrag = new view('frag.opennessAnswerMultiSelect');
						$answerFrag->replace("answer", $answer['answer']);
						$answerFrag->replace("answer_id", $answer['id']);
						$answerFrag->replace("section", $question['section']);
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

					$answers = $this->getAnswers($question['id']);
					$a = new view();
				
					foreach($answers[0] as $answer){
						$answerFrag = new view('frag.opennessAnswerScale');
						$answerFrag->replace("answer", $answer['answer']);
						$answerFrag->replace("answer_id", $answer['id']);
						$answerFrag->replace("section", $question['section']);
						$answerFrag->replace("number", $i);
						$template->replace("answer_id",$answer['id']);
						$template->replace("answer".$i,$i);
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