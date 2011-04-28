<?php
class resourceView extends view {

	private $resource;
	private $user;
	private $delete;
	
	private $resourceType;

	public function __construct(resource $resource, user $thisUser){
		parent::__construct('frag.idea');
		
		$this->resource = $resource;
		$this->user = $thisUser;
		
		if($this->resource instanceof idea){
			$this->resourceType = "idea";
		} else {
			if((BOOL)$this->resource->getIncubated()){
				$this->resourceType = "incubator";
			} else {
				$this->resourceType = "project";
			}
		}
		
		$this->parse();
	}
	
	private function parse(){
		$this->replace("title", $this->resource->getName());
		$this->replace("points", $this->resource->countVotes());
		$this->replace("chats", $this->resource->getChatCount());
		$this->replace("pitch", $this->resource->getOverview());
		$this->replace("image", $this->resource->getImage());
		$this->replace("id", $this->resource->getId());
		$this->replace("type", get_class($this->resource));
		$this->replace("url", $this->resourceType . "/" . $this->resource->getId());
		
		if(get_class($this->resource) == "project"){
			$this->replace("assoc", $this->resource->getSiblingCount());
		} else {
			$this->replace("assoc", $this->resource->getProjectCount());
		}
		
		if($this->user->getIsAdmin()){
			if($this->resource->getHidden()){
				$this->replace('delete', 'HIDDEN');
			} else {
				// Display the deletion icon
				$delete = new view('frag.deleteComment');
				$this->replace('delete', $delete);
			}
		} else {
			$this->replace('delete', '');
		}				
	}

}

?>