<?php class paginationView extends view {

	private $linkView;
	private $total = 0;
	private $pageLimit = 9;
	private $pages;
	private $currentPage;
	
	private $resourceType;

	public function __construct(collection $data, $currentPage){
		parent::__construct();
		
		$this->total = $data->getFoundRows();
		$this->pages = ceil($this->total / $this->pageLimit);
		$this->currentPage = $currentPage;
		
		$this->linkView = new view('frag.pageLink');
		
		switch($data->getType()){
			case collection::TYPE_IDEA:
				$this->resourceType = "idea";
			break;
			
			case collection::TYPE_PROJECT:
				$this->resourceType = "incubator";
			break;
			
			case collection::TYPE_INCUBATED:
				$this->resourceType = "project";
			break;
			
		}
		
		$this->parse();
	}
	
	private function parse(){

		if($this->pages == 1) return;

		// Previous link
		if($this->currentPage != 0){
			$this->linkView->replace('description', '&laquo;')->replace('link', "/{$this->resourceType}/page/" . $this->currentPage);
			$this->append($this->linkView);
			$this->linkView->reset();
		}
		
		// Pages
		for($i=1; $i<=$this->pages;$i++){
			// Is this the current page?
			$class = ($i==$this->currentPage+1) ? "selected" : "";
			$this->linkView->replace('description', $i)->replace('link', "/{$this->resourceType}/page/" . $i)->replace('class', $class);
			$this->append($this->linkView);
			$this->linkView->reset();
		}
		
		// Next link
		if($this->currentPage+1 != $this->pages){
			$this->linkView->replace('description', '&raquo;')->replace('link', "/{$this->resourceType}/page/" . ($this->currentPage+2));
			$this->append($this->linkView);
			$this->linkView->reset();
		}
			
	}

}

?>