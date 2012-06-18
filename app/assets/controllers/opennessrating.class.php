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
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $colour);
		
		$this->pageName = "- Openness Rating";
		$this->setViewport(new view("openness"));		
		$this->viewport()->replace("id", $id);
	}
	
	protected function opennessInfo(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $colour);
		
		$pageName = "Openness Rating - Project Information";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-info"));
		$this->viewport()->replace("project_id", $id);	
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($this->helpJS());
		$this->createQuestions($id, "info");
	}
	
	protected function opennessLegal(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $colour);
		
		$pageName = "Openness Rating - Legal";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-legal"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($this->helpJS());
		$this->createQuestions($id, "legal");
	}	
	
	protected function opennessStandards(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $colour);
		
		$pageName = "Openness Rating - Data Formats and Standards";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-standards"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($this->helpJS());
		$this->createQuestions($id, "standards");
	}	
	
	protected function opennessKnowledge(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $colour);
		
		$pageName = "Openness Rating - Knowledge";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-knowledge"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($this->helpJS());
		$this->createQuestions($id, "knowledge");
	}
	
	protected function opennessGovernance(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $colour);
		
		$pageName = "Openness Rating - Governance";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-governance"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($this->helpJS());
		$this->createQuestions($id, "governance");
	}
	
	protected function opennessMarket(){
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $colour);
		
		$pageName = "Openness Rating - Market";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-market"));
		$this->viewport()->replace("project_id", $id);
		$this->viewport()->replace("page-name", $pageName);
		$this->viewport()->append($this->helpJS());
		$this->createQuestions($id, "market");
	}
	
	protected function opennessEnd(){		
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id = $this->testProjectId($id);
		$this->superview()->replace("sidecontent_pageid", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->superview()->replace("openness-rating", $openness);
		$this->superview()->replace("alert-colour", $colour);
		
		$pageName = "Openness Rating - Complete";
		
		$this->pageName = "- ".$pageName;
		$this->setViewport(new view("openness-end"));
		$this->viewport()->replace("id", $id);
		
		$openness = $this->getOpennessRating($id);
		$colour = $this->ratingColour($openness);
		$this->viewport()->replace("openness-rating", $openness);
		$this->viewport()->replace("alert-colour", $colour);
	}
	
	protected function createQuestions($project_id, $section){
	
		$questions = $this->getQuestions($section);
		
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
						$help = $this->helpText($question['id'], $question['help']);
						$template->replace("info", $help);

						$answers = $this->getAnswers($question['id']);
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
							$value = $this->viewAnswer($project_id, $question['id']);
							
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
							$help = $this->helpText($question['id'], $question['help']);
							$template->replace("info", $help);

							$value = $this->viewAnswer($project_id, $question['id']);
							
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
						$help = $this->helpText($question['id'], $question['help']);
						$template->replace("info", $help);

						$answers = $this->getAnswers($question['id']);
						$a = new view();
					
						foreach($answers[0] as $answer){
							$answerFrag = new view('frag.opennessAnswerMultiSelect');
							$answerFrag->replace("answer", $answer['answer']);
							$answerFrag->replace("answer_id", $answer['id']);
							$answerFrag->replace("section", $question['section']);
							$answerFrag->replace("question_id", $question['id']);
							
							$value = $this->viewAnswer($project_id, $question['id']);
							
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
						$help = $this->helpText($question['id'], $question['help']);
						$template->replace("info", $help);

						$answers = $this->getAnswers($question['id']);
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
							
							$value = $this->viewAnswer($project_id, $question['id']);
							
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
		$this->process("info", "legal");
	}

	protected function legalProcess(){
		$this->process("legal", "standards");
	}
	
	protected function standardsProcess(){
		$this->process("standards", "knowledge");
	}
	
	protected function knowledgeProcess(){
		$this->process("knowledge", "governance");
	}
	
	protected function governanceProcess(){
		$this->process("governance", "market");
	}
	
	protected function marketProcess(){
		$this->process("market", "end");
	}
	
	protected function process($section, $next_section){
		$project_id = $this->getProjectId();
		$project_id = $this->testProjectId($project_id);
		$q = $this->getQuestionId($section);
		// $question_id = first question for that section
		//questions are assumed to be in id order
		$question_id = $q[0][0]['id'];

		foreach($q[0] as $question){
		
			$value = $_POST[$section."-".$question_id];
			
			//does answer for this project exist - if so delete it (indicated by true/false field)
			$this->doesProjectAnswerExist($project_id, $question['id'], TRUE);
			//then create new answer
			$this->createAnswer($question_id, $project_id, $value);	
			$question_id++;
		}
		
		if($next_section == "end"){
			$this->redirect("/opennessrating/completed/?id=".$project_id);
		}
		else{
			$this->redirect("/opennessrating/".$next_section."/?id=".$project_id);
		}
		
		$this->calculateOpenness($project_id);
	}	
	
	protected function getQuestions($section){
		$this->db = db::singleton();
		$result = $this->db->select(array("*"), "open_question", array(array("", "section", "=", $section)))->run();
		return $result;
	}

	protected function getAnswers($question_id){
		$this->db = db::singleton();
		$result = $this->db->select(array("*"), "open_answer", array(array("", "open_question_id", "=", $question_id)))->run();
		return $result;
	}		
	
	protected function getQuestionId($section){
		$this->db = db::singleton();
		$result = $this->db->select(array("id"), "open_question", array(array("", "section", "=", $section)))->run();
		return $result;
	}
	
	protected function getProjectId(){
		return $_POST['project_id'];
	}
	
	protected function testProjectId($id){
		$this->db = db::singleton();
		$result = $this->db->select(array("id"), "project", array(array("", "id", "=", $id)))->run();
		if($id == NULL OR empty($result)){
			echo "Invalid project ID.";
			exit;
		}
		else {
			return $id;
		}
	}
	
	protected function createAnswer($question_id, $project_id, $value = NULL){
		$this->db = db::singleton();
		$result = $this->db->select(array("type"), "open_question", array(array("", "id", "=", $question_id)))->run();

		try{
			switch ( $result[0][0]['type']){
				case 'drop':
				case 'text':
				case 'scale':
					$result = $this->db->insert(array("question_id" => $question_id, "project_id" => $project_id, "value" => $value), "open_project_has_answer")->run();
				break;
				
				case 'multi-select':
					foreach($value as $v){
						$result = $this->db->insert(array("question_id" => $question_id, "project_id" => $project_id, "value" => $v), "open_project_has_answer")->run();
					}
				break;

				}
		} catch (Exception $e){
			echo "Unable to create answers in database. Please try again later.";
			exit;
		}
	}
	
	protected function viewAnswer($project_id, $question_id){	
		$this->db = db::singleton();
		$result = $this->db->select(array("value"), "open_project_has_answer", array(array("", "project_id", "=", $project_id)), "AND question_id = ".$question_id)->run();
	
		$a = array();		
		if(empty($result)){}
		else{
			foreach($result[0] as $r){
				array_push($a, $r['value']);
			}
			return $a;
		}
	}
	
	protected function doesProjectAnswerExist($project_id, $question_id, $delete){
		$this->db = db::singleton();
		$result = $this->db->select(array("id"), "open_project_has_answer", array(array("", "project_id", "=", $project_id)), "AND question_id = ".$question_id)->run();		
		
		if(empty($result)){}
		else {
			$project_answer_id = $result[0][0]['id'];
		
			if($delete == TRUE){
				$this->deleteProjectAnswer($project_answer_id);
			}
		}
	}
	
	protected function deleteProjectAnswer($project_answer_id){
		$this->db = db::singleton();
		try{
			$result = $this->db->delete("open_project_has_answer", array(array("","id",$project_answer_id)))->run();
		}catch(Exception $e){
			echo "Database error. Please try again later.";
			exit;
		}
	}
	
	protected function helpText($questionID, $text){	
		$icon = '<a href="#" class="info-toggle"><img src="/presentation/images/information.png" /></a>';
		$close = '<button type="button" class="close" data-dismiss="alert">x</button>';

		$box = '<div class="alert alert-info" id="help-text-'.$questionID.'" style="display: none;">'.$text.'</div>';
		return $icon.$box;
	}
	
	protected function calculateOpenness($project_id){
		//dont know value
		$dn = 0;
		$score = 0;
		
		$max_score = $this->db->select(array("SUM(max_score) AS max_score"), "open_question")->run();	
		$max_score = $max_score[0][0]['max_score'];
		
		$result = $this->db->single("SELECT open_question.id, open_question.max_score, open_project_has_answer.value, open_answer.value AS score FROM open_question
						LEFT JOIN open_project_has_answer ON (open_question.id = open_project_has_answer.question_id) 
						LEFT JOIN open_answer ON (open_answer.id = open_project_has_answer.value) 
						WHERE open_project_has_answer.project_id = ".$project_id);
		
		foreach($result as $r){
			$qs = $r['score'];
			$qms = $r['max_score'];
		
			if($qs == "dn"){
				$score += $dn;
			}
			else if($qs >= $qms){
				$score += $qms;
			}
			else if($qs < $qms){
				$score += $qs;
			}
		}
		
		//calcualate percentagt score
		$rating = ($score/$max_score)*100;

		//insert openness rating
		try{
			$result = $this->db->update(array("openness_rating" => $rating), "project", array(array("", "id", $project_id)))->run();
		}catch(Exception $e){
			echo "Database error. Please try again later.";
			exit;
		}

	}
	
	protected function getOpennessRating($project_id){
		$result = $this->db->select(array("openness_rating"), "project", array(array("", "id", "=", $project_id)))->run();
		$result = $result[0][0]['openness_rating'];
		
		if($result == NULL){
			$result = 0;
		}
		return $result."%";
	}
	
	protected function ratingColour($rating){
		$colour = "";
		$OR_pass = 75;
		$OR_ok = 30
		
		if($rating >= 75){
			$colour = "alert-success";
		}
		else if($rating < $OR_pass && $rating > $OR_ok){
			$colour = "alert-block";
		}
		else if($rating < $OR_ok){
			$colour = "alert-error";
		}
		
		return $colour;
	}

	protected function helpJS(){
		$js = '
		<script type="text/javascript">$(".info-toggle").click(function () {
			$(this).parentsUntil("div.question").parent().find(".alert").toggle();
			});
		</script>';
		
		return $js;
	}
		
}

?>