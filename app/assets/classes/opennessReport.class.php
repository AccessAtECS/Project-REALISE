<?php

class opennessReport {
	
	public function button($id, $next){
		if($next != "end"){
			$button = '<a href="/opennessreport/'.$next.'/?id='.$id.'"><button class="openness-button">Next Section</button></a>';
			return $button;
		}
		else if($next == "end"){
			$button = '<a href="/opennessreport/?id='.$id.'"><button class="openness-button">End Report</button></a>';
			return $button;
		}
	}

}

?>