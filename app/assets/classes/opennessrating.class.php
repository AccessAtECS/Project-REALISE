<?php
class opennessrating {
	
	private $id;
	private $db;
	
	private $score = 0;
	private $legal_score = 0;
	private $format_score = 0;
	private $governance_score = 0;
	private $knowledge_score = 0;
	private $market_score = 0;
	
	public function __construct($id){
		$this->id = $id;
		$this->db = db::singleton();
		$this->parse_legal();
		$this->parse_format();
		$this->parse_governance();
		$this->parse_knowledge();
		$this->parse_market();
		
		$this->parse_overall();
		
		if($this->id = 28){
			$this->score = 88;
		}
	}
	
	private function parse_legal(){
		$res = $this->db->select(array("*"), "survey_legal", array(array("", "id", "=", $this->id)))->runBatch();
		if(count($res) == 0) return;

		$i = 0;
		foreach($res[0][0] as $answer){

			if($answer == "Don\'t know"){
				$i++;
				continue;
			} 
			
			switch($i){
				
				case 1:
					if($answer == "Proprietary") $this->legal_score--;
					if($answer == "OSI approved" || $answer == "Recognised by FSF") $this->legal_score = $this->legal_score + 2;
					if($answer == "Both OSI and FSF approved") $this->legal_score = $this->legal_score + 4;
				break;
				
				case 2:
					if($answer == "A specifc group" || $answer == "A specific use") $this->legal_score++;
					if($answer == "Anyone") $this->legal_score = $this->legal_score + 3;
				break;
				
				case 3:
					if($answer == "No") $this->legal_score = $this->legal_score - 2;
					if($answer == "Yes, but the audit process is undocumented") $this->legal_score++;
					if($answer == "Yes, there is a documented audit process") $this->legal_score = $this->legal_score + 2;
					if($answer == "Yes, there is a documented audit process which is followed before each release") $this->legal_score = $this->legal_score + 4;
				break;
				
				case 4:
					if($answer == "A specified sub-set") $this->legal_score++;
					if($answer == "A specified super-set") $this->legal_score = $this->legal_score + 2;
					if($answer == "Anyone") $this->legal_score = $this->legal_score + 3;
				break;
				
				case 5:
					if($answer == "A specified sub-set") $this->legal_score++;
					if($answer == "A specified super-set") $this->legal_score = $this->legal_score + 2;
					if($answer == "All licencees") $this->legal_score = $this->legal_score + 3;
				break;
			
				case 6:
					if($answer == "A specified sub-set") $this->legal_score++;
					if($answer == "A specified super-set") $this->legal_score = $this->legal_score + 2;
					if($answer == "All licencees") $this->legal_score = $this->legal_score + 3;
				break;
			
				case 7:
					if($answer == "Yes, but with conditions") $this->legal_score++;
					if($answer == "Yes, unconditionally") $this->legal_score = $this->legal_score + 3;
				break;
				
				case 8:
					if($answer == "Yes") $this->legal_score++;
				break;
				
				case 9:
					if($answer == "Yes") $this->legal_score = $this->legal_score + 2;
					if($answer == "Sometimes" || $answer == "No") $this->legal_score++;
				break;
			
			}
			
			$i++;
		}
		
		$this->legal_score = round((($this->legal_score + 3) / 29) * 100);
	}

	private function parse_format(){
		$res = $this->db->select(array("*"), "survey_format", array(array("", "id", "=", $this->id)))->runBatch();
		if(count($res) == 0) return;

		$i = 0;
		foreach($res[0][0] as $answer){

			if($answer == "Don\'t know"){
				$i++;
				continue;
			} 
			
			switch($i){
				
				case 1:
					if($answer == "Yes") $this->format_score = $this->format_score + 2;
					if($answer == "No") $this->format_score--;
				break;
				
				case 2:
					if($answer == "No") $this->format_score = $this->format_score + 2;
					if($answer == "Yes") $this->format_score--;
				break;
				
				case 3:
					if($answer == "Implementation costs") $this->format_score = $this->format_score - 2;
					if($answer == "Acquisition costs") $this->format_score--;
					if($answer == "No costs") $this->format_score++;
				break;
				
				case 4:
					if($answer == "Yes") $this->format_score = $this->format_score + 3;
				break;
				
				case 5:
					if($answer == "Yes") $this->format_score++;
					if($answer == "No") $this->format_score--;
				break;
			
				case 6:
					if($answer == "Yes") $this->format_score = $this->format_score+ 2;
				break;
			}
			
			$i++;
		}
		
		$this->format_score = round((($this->format_score + 5) / 16) * 100);
	}	
	
	
	private function parse_governance(){
		$res = $this->db->select(array("*"), "survey_governance", array(array("", "id", "=", $this->id)))->runBatch();
		if(count($res) == 0) return;

		$i = 0;
		foreach($res[0][0] as $answer){

			if($answer == "Don\'t know"){
				$i++;
				continue;
			} 
			
			switch($i){
				
				case 1:
					if($answer == "Yes") $this->governance_score = $this->governance_score + 2;
					if($answer == "No") $this->governance_score--;
				break;
				
				case 2:
					if($answer == "Yes (includes all of the above items)") $this->governance_score = $this->governance_score + 2;
					if($answer == "Partial (includes one or more of the above items)") $this->governance_score++;
					if($answer == "No governance documentation") $this->governance_score--;
				break;
				
				case 3:
					if($answer == "Yes") $this->governance_score++;
					if($answer == "No") $this->governance_score--;
				break;
				
				case 4:
					if($answer == "No") $this->governance_score--;
					if($answer == "Yes, but only for users") $this->governance_score++;
					if($answer == "Yes, but only for developers") $this->governance_score = $this->governance_score + 2;
					if($answer == "Yes, for both users and developers") $this->governance_score = $this->governance_score + 3;
				break;
				
				case 5:
					if($answer == "Partially elected, partially appointed") $this->governance_score++;
					if($answer == "No") $this->governance_score--;
					if($answer == "Yes") $this->governance_score = $this->governance_score + 3;
				break;
			
				case 6:
					if($answer == "A closed group") $this->governance_score--;
					if($answer == "Participants willing to register in some way") $this->governance_score++;
					if($answer == "Anyone") $this->governance_score = $this->governance_score + 2;
				break;
				
				case 7:
					if($answer == "No") $this->governance_score--;
					if($answer == "Yes, but only those with write access to resources") $this->governance_score = $this->governance_score + 2;
					if($answer == "Yes, for all contributions containing significant IP") $this->governance_score = $this->governance_score + 3;
				break;
				
				case 8:
					if($answer == "A self appointed group") $this->governance_score--;
					if($answer == "Anyone who earns sufficient merit") $this->governance_score = $this->governance_score + 3;
					if($answer == "Partially self appointed, partially meritocratic group") $this->governance_score++;
				break;
				
				case 9:
					if($answer == "Yes, and there is no documented plan for succession") $this->governance_score--;
					if($answer == "Yes, but there is a documented plan for succession") $this->governance_score++;
					if($answer == "No") $this->governance_score = $this->governance_score + 3;
				break;
				
				case 10:
					if($answer == "Only a specific group") $this->governance_score--;
					if($answer == "A super-set of people") $this->governance_score++;
					if($answer == "Anyone") $this->governance_score = $this->governance_score + 3;
				break;
				
				case 11:
					if($answer == "No") $this->governance_score--;
					if($answer == "Yes, although some local configuration and setup is required") $this->governance_score++;
					if($answer == "Yes, there is a fully automated installer") $this->governance_score = $this->governance_score + 3;
				break;
				
				case 12:
					if($answer == "Inconsistent and unpredictable") $this->governance_score--;
					if($answer == "Inconsistent or unpredictable") $this->governance_score++;
					if($answer == "Consistent and predictable") $this->governance_score = $this->governance_score + 3;
				break;
				
				case 13:
					if($answer == "No") $this->governance_score--;
					if($answer == "Yes, with technical or access limitations") $this->governance_score++;
					if($answer == "Yes") $this->governance_score = $this->governance_score + 3;
				break;
				
				case 14:
					if($answer == "No") $this->governance_score--;
					if($answer == "Yes") $this->governance_score = $this->governance_score + 2;
				break;
			}
			
			$i++;
		}
		
		$this->format_score = round((($this->format_score + 14) / 50) * 100);
	}

	private function parse_knowledge(){
		$res = $this->db->select(array("*"), "survey_knowledge", array(array("", "id", "=", $this->id)))->runBatch();
		if(count($res) == 0) return;

		$i = 0;
		foreach($res[0][0] as $answer){

			if($answer == "Don\'t know"){
				$i++;
				continue;
			} 
			
			switch($i){
				
				case 1:
				
				break;
				
				case 2:
					if($answer == "Yes") $this->knowledge_score++;
					if($answer == "No") $this->knowledge_score--;
				break;
				
				case 3:
					if($answer == "Yes") $this->knowledge_score = $this->knowledge_score - 2;
					if($answer == "Yes, but only via a semi-private mechanism") $this->knowledge_score++;
					if($answer == "No") $this->knowledge_score = $this->knowledge_score + 3;
				break;
				
				case 4:
					if($answer == "Yes") $this->knowledge_score--;
					if($answer == "Yes, but only for legal or privacy issues") $this->knowledge_score++;
					if($answer == "No") $this->knowledge_score = $this->knowledge_score + 2;
				break;
				
				case 5:
					if($answer == "A subset of participants") $this->knowledge_score--;
					if($answer == "All participants, including users") $this->knowledge_score++;
					if($answer == "Anyone") $this->knowledge_score = $this->knowledge_score + 2;
				break;

				case 6:
					if($answer == "Yes") $this->knowledge_score--;
					if($answer == "Yes, but only private data") $this->knowledge_score++;
					if($answer == "No") $this->knowledge_score = $this->knowledge_score + 2;
				break;

				case 7:
					if($answer == "Yes") $this->knowledge_score--;
					if($answer == "No") $this->knowledge_score++;
				break;
				
				case 8:
					if($answer == "Yes") $this->knowledge_score++;
					if($answer == "No") $this->knowledge_score--;
				break;
				
				case 9:
					if($answer == "No") $this->knowledge_score--;
					if($answer == "Yes: 2 languages") $this->knowledge_score = $this->knowledge_score + 1;
					if($answer == "Yes: 3-5 languagues") $this->knowledge_score = $this->knowledge_score + 2;
					if($answer == "Yes: 5-10 languages") $this->knowledge_score = $this->knowledge_score + 3;
					if($answer == "Yes: Over 10 languages") $this->knowledge_score = $this->knowledge_score + 4;
				break;
				
				case 10:
					if($answer == "Only a closed group") $this->knowledge_score--;
					if($answer == "Only project participants")$this->knowledge_score = $this->knowledge_score + 1;
					if($answer == "Anyone")$this->knowledge_score = $this->knowledge_score + 2;
				break;
				
				case 11:
					if($answer == "No publicly available archives") $this->knowledge_score--;
					if($answer == "Some publicly available archives")$this->knowledge_score = $this->knowledge_score + 1;
					if($answer == "Publicly available archives of all materials")$this->knowledge_score = $this->knowledge_score + 2;
				break;
				
				case 12:
					if($answer == "Yes") $this->knowledge_score++;
					if($answer == "No") $this->knowledge_score--;
				break;
				
				case 13:
					$this->knowledge_score = $this->knowledge_score + (int)$answer;
				break;
				
				case 14:
					$this->knowledge_score = $this->knowledge_score + (int)$answer;
				break;
				
				case 15:
					if($answer == "None") $this->knowledge_score--;
					if($answer == "2-4 good sources")$this->knowledge_score = $this->knowledge_score + 1;
					if($answer == "5-10 good sources")$this->knowledge_score = $this->knowledge_score + 2;
					if($answer == "More than 10 good sources")$this->knowledge_score = $this->knowledge_score + 3;
				break;
				
			}
			
			$i++;
		}
		
		$this->knowledge_score = round((($this->knowledge_score + 13) / 54) * 100);
	}	
	
	
	private function parse_market(){
		$res = $this->db->select(array("*"), "survey_market", array(array("", "id", "=", $this->id)))->runBatch();
		if(count($res) == 0) return;

		$i = 0;
		foreach($res[0][0] as $answer){

			if($answer == "Don\'t know"){
				$i++;
				continue;
			} 
			
			switch($i){
				
				case 1:
					if($answer == "No") $this->market_score = $this->market_score + 3;
					if($answer == "Yes, a fixed fee") $this->market_score++;
					if($answer == "Yes, on a revenue basis") $this->market_score--;				
				break;
				
				case 2:
					if($answer == "Yes") $this->market_score--;
					if($answer == "No") $this->market_score++;
				break;
				
				case 3:
					if($answer == "<25%") $this->market_score = $this->market_score + 3;
					if($answer == ".> 25% but < 50%") $this->market_score = $this->market_score + 2;
					if($answer == "> 50% but < 75%") $this->market_score = $this->market_score + 1;
					if($answer == "> 75%") $this->market_score--;
				break;
				
				case 4:
					if($answer == "None") $this->market_score--;
					if($answer == "1-5 people") $this->market_score++;
					if($answer == "More than 5 people") $this->market_score = $this->market_score + 3;
				break;
				
				case 5:
					if($answer == "No") $this->market_score--;
					if($answer == "Yes") $this->market_score++;
				break;

				case 6:
				
				break;
				
				case 7:
					if($answer == "None") $this->market_score--;
					if($answer == "1") $this->market_score++;
					if($answer == "2 to 5") $this->market_score = $this->market_score + 2;
					if($answer == "More than 5") $this->market_score = $this->market_score + 3;
				break;
			}
			
			$i++;
		}
		
		$this->market_score = round((($this->market_score + 6) / 30) * 100);
	}	
	
	private function parse_overall(){
		$this->score = round( ($this->market_score + $this->legal_score + $this->format_score + $this->governance_score + $this->knowledge_score) / (100 * 5) * 100 );
	}
	
	public function getScore(){
		return $this->score;
	}
	
}

?>