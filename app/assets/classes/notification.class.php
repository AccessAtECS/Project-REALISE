<?php

class notification {

	const FROM_ADDR = "marketplace@realisepotential.org";
	
	const NOTIFICATION_IDEA = "created a new idea entitled {tmpl}";
	const NOTIFICATION_INCUBATED = "created a new project entitled {project} from the idea {idea}";
	const NOTIFICATION_PROJECT = "converted the incubated project entitled {tmpl} into a full project";
	const NOTIFICATION_COMMENT = "commented on the {type} {tmpl}";
	
	const TYPE_MAILINGLIST = 1;

	private $type;
	private $body;
	private $headers;

	public function __construct($type = notification::TYPE_MAILINGLIST){
		$this->headers = 'From: "Project Realise Marketplace" <' . notification::FROM_ADDR . '>' . PHP_EOL;
	}
	
	public function compose(view $view, array $replacements = array()){
		$this->body = $view->replaceAll($replacements);
	}
	
	public function send(){
		mail(SYS_MAILINGLIST_ADDR, "Someone has updated Project REALISE!", $this->body, $this->headers);
	}


}

?>