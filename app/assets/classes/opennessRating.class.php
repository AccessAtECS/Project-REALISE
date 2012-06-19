<?php

class opennessRating {
	
	private $_db;
	
	public function __construct(){
		$this->_db = db::singleton();
	}
	
	public function createQuestions($project_id, $section, $report = FALSE){
		$questions = $this->getQuestions($section);
		
		$q = new view();
		
		try{
			foreach($questions[0] as $question){	
			
				switch ($question['type']){
				
					case 'drop':
						$template = new view('frag.opennessQuestionDrop');
						$template->replace("question", $question['question']);
						$template->replace("section", $question['section']);
						$template->replace("question_id", $question['id']);
						$help = $this->helpText($question['id'], $question['help']);
						
						if($report == TRUE){
							$template->replace("disabled", 'disabled="disabled"');
							$template->replace("sub_question", "");
							$template->replace("info", "");
						}
						else {
							$template->replace("sub_question", $question['sub_question']);
							$template->replace("info", $help);
						}

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
							$template->replace("section", $question['section']);
							$template->replace("question_id", $question['id']);
							$help = $this->helpText($question['id'], $question['help']);
							
							if($report == TRUE){
								$template->replace("disabled", 'disabled="disabled"');
								$template->replace("sub_question", "");
								$template->replace("info", "");
							}
							else {
								$template->replace("sub_question", $question['sub_question']);
								$template->replace("info", $help);
							}

							$value = $this->viewAnswer($project_id, $question['id']);
							
							if(empty($value)){
								$template->replace("value", "");
							}
							else {
								$template->replace("value", $value[0]);
							}							
							$q->append($template->get());
						}

						return $q;
					break;
					
					case 'multi-select':
						$template = new view('frag.opennessQuestionMultiSelect');
						$template->replace("question", $question['question']);
						$template->replace("section", $question['section']);
						$template->replace("question_id", $question['id']);
						$help = $this->helpText($question['id'], $question['help']);
						
						if($report == TRUE){
							$template->replace("sub_question", "");
							$template->replace("info", "");
						}
						else {
							$template->replace("sub_question", $question['sub_question']);
							$template->replace("info", $help);
						}
												
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
							
							if($report == TRUE){
								$answerFrag->replace("disabled", 'disabled="disabled"');
							}
							
							$a->append($answerFrag->get());
							
						}					
						$template->replace("options", $a);
						
					break;
					
					case 'scale':
						$i = 0;
						
						$template = new view('frag.opennessQuestionScale');
						$template->replace("question", $question['question']);
						$template->replace("question_id", $question['id']);
						$help = $this->helpText($question['id'], $question['help']);
						
						if($report == TRUE){
							$template->replace("sub_question", "");
							$template->replace("info", "");
						}
						else {
							$template->replace("sub_question", $question['sub_question']);
							$template->replace("info", $help);
						}

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
							
							if($report == TRUE){
								$answerFrag->replace("disabled", 'disabled="disabled"');
							}
							
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

			return $q;	
			
		} catch(Exception $e){
			echo "Unable to render openness rating question list. Please try again later.";
			exit;
		}		
	}
	
	public function process($section, $next_section){
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
		
		$this->calculateOpenness($project_id);
		
		if($next_section == "end"){
			return "/opennessrating/completed/?id=".$project_id;
		}
		else{
			return "/opennessrating/".$next_section."/?id=".$project_id;
		}
	}
	
	public function calculateOpenness($project_id){
		//dont know value
		$dn = 0;
		$score = 0;
		
		$max_score = $this->_db->select(array("SUM(max_score) AS max_score"), "open_question")->run();	
		$max_score = $max_score[0][0]['max_score'];
		
		$result = $this->_db->single("SELECT open_question.id, open_question.max_score, open_project_has_answer.value, open_answer.value AS score FROM open_question
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
			$result = $this->_db->update(array("openness_rating" => $rating), "project", array(array("", "id", $project_id)))->run();
		}catch(Exception $e){
			echo "Database error. Please try again later.";
			exit;
		}

	}
	
	private function createAnswer($question_id, $project_id, $value = NULL){
		$result = $this->_db->select(array("type"), "open_question", array(array("", "id", "=", $question_id)))->run();

		try{
			switch ( $result[0][0]['type']){
				case 'drop':
				case 'text':
				case 'scale':
					$result = $this->_db->insert(array("question_id" => $question_id, "project_id" => $project_id, "value" => $value), "open_project_has_answer")->run();
				break;
				
				case 'multi-select':
					foreach($value as $v){
						$result = $this->_db->insert(array("question_id" => $question_id, "project_id" => $project_id, "value" => $v), "open_project_has_answer")->run();
					}
				break;

				}
		} catch (Exception $e){
			echo "Unable to create answers in database. Please try again later.";
			exit;
		}
	}
	
	private function doesProjectAnswerExist($project_id, $question_id, $delete){
		$result = $this->_db->select(array("id"), "open_project_has_answer", array(array("", "project_id", "=", $project_id)), "AND question_id = ".$question_id)->run();		
		
		if(empty($result)){}
		else {
			$project_answer_id = $result[0][0]['id'];
		
			if($delete == TRUE){
				$this->deleteProjectAnswer($project_answer_id);
			}
		}
	}
	
	private function deleteProjectAnswer($project_answer_id){
		try{
			$result = $this->_db->delete("open_project_has_answer", array(array("","id",$project_answer_id)))->run();
		}catch(Exception $e){
			echo "Database error. Please try again later.";
			exit;
		}
	}
	
	public function getQuestions($section){
		$result = $this->_db->select(array("*"), "open_question", array(array("", "section", "=", $section)))->run();
		return $result;
	}

	public function getAnswers($question_id){
		$result = $this->_db->select(array("*"), "open_answer", array(array("", "open_question_id", "=", $question_id)))->run();
		return $result;
	}
	
	public function helpJS(){
		$js = '
		<script type="text/javascript">$(".info-toggle").click(function () {
			$(this).parentsUntil("div.question").parent().find(".alert").toggle();
			});
		</script>';
		
		return $js;
	}
	
	public function viewAnswer($project_id, $question_id){	
		$result = $this->_db->select(array("value"), "open_project_has_answer", array(array("", "project_id", "=", $project_id)), "AND question_id = ".$question_id)->run();
	
		$a = array();		
		if(empty($result)){}
		else{
			foreach($result[0] as $r){
				array_push($a, $r['value']);
			}
			return $a;
		}
	}
	
	public function getProjectId(){
		return $_POST['project_id'];
	}
	
	public function testProjectId($id){
		$result = $this->_db->select(array("id"), "project", array(array("", "id", "=", $id)))->run();
		if($id == NULL OR empty($result)){
			echo "Invalid project ID.";
			exit;
		}
		else {
			return $id;
		}
	}
	
	public function getQuestionId($section){
		$result = $this->_db->select(array("id"), "open_question", array(array("", "section", "=", $section)))->run();
		return $result;
	}
	
	public function getOpennessRating($project_id){
		$result = $this->_db->select(array("openness_rating"), "project", array(array("", "id", "=", $project_id)))->run();
		$result = $result[0][0]['openness_rating'];
		
		if($result == NULL){
			$result = 0;
		}
		return $result."%";
	}
	
	public function ratingColour($rating){
		$colour = "";
		$OR_pass = SYS_OPENNESS_THRESHOLD;
		$OR_ok = 20;
		
		if($rating >= $OR_pass){
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
	
	public function helpText($questionID, $text){	
		$icon = '<a href="#" class="info-toggle"><img src="/presentation/images/information.png" /></a>';
		$close = '<button type="button" class="close" data-dismiss="alert">x</button>';

		$box = '<div class="alert alert-info" id="help-text-'.$questionID.'" style="display: none;">'.$text.'</div>';
		return $icon.$box;
	}

}

?>