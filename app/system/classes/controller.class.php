<?php

// Pull in the interface
require_once(dirname(__FILE__) . "/viewController.interface.php");


abstract class controller implements viewController {

	private $objectStore;
	
	private $redirectTo;
	
	private $viewport;
	private $superview;

	private $context;
	private $routeMap = array();
	private $defaultRoute = "";


	final public function init(array $context, array $objects){
		// Initialise the controller
		
		$this->objectStore = $objects;
		$this->context = $context;
		
		foreach($this->objectStore as $key=>$object){
			if(get_class($object) == "view"){
				if($object->identify() == "superView"){
					$this->superview = $object;
					unset($this->objectStore[$key]);
				}
			}
		}		
		
		// Check to see if the user object is in the session.
		if(isset($_SESSION['user'])){
			$u = $_SESSION['user'];
			if(get_class($u) == "user"){
				$this->setObject("user", $u);
			} else {
				throw new Exception("Deserialised user object does not match!");
			}
		} 
	}

	
	final protected function bind($pattern, $function, $opts = ""){
		// Bind a pattern to a URL
		$pattern = "/" . addcslashes($pattern, "/") . "/" . $opts;
		array_push($this->routeMap, array("pattern" => $pattern, "function" => $function ));
	}
	
	final protected function bindDefault($function){
		$this->defaultRoute = $function;
	}
	
	final protected function execute(){	
		foreach($this->routeMap as $route){
			if(preg_match($route['pattern'], implode("/", $this->context), $matches )){
				$fn = array($this, $route['function']);

				// Check to see if we can call the function.
				if(!is_callable($fn)) throw new Exception("Provided responder for route " . $route['pattern'] . " is invalid!");
				// Call the function and provide matches from the pattern.
				call_user_func($fn, array_slice($matches, 1));
				return;
			}		
		}

		if($this->defaultRoute != ""){
			$fn = array($this, $this->defaultRoute);
			if(!is_callable($fn)) throw new Exception("Provided responder for default route is invalid!");
			call_user_func($fn);
		}
	}

	final protected function setViewport(view $view){
		$this->viewport = $view;
	}

	final public function render($type){
		if($type != "view" && method_exists($this, 'renderData')){
			// Render data
			$this->renderData($type);
		} else {
			// Render the viewport
			$this->renderViewport();
			
			// Run the user code
			$this->execute();
			
			// Write the user object out.
			$_SESSION['user'] = $this->objects("user");
			
			if(isset($this->redirectTo)) header("Location: " . $this->redirectTo);
			
			// Render the window with the user generated view inside the Viewport. If noRender has been specified, leave this step out.
			if($this->noRender() == false) $this->renderWindow();
		}	
	}

	final protected function renderWindow(){
		$this->superview->replace("viewport", $this->viewport);
		$this->superview->replace('include-url', SYS_INCLUDEURL);
		$this->superview->replace('base-url', BASEURL);
		$this->superview->replace('additional-assets', "");
		$this->superview->replace('version', REALISE_VERSION);
		$this->superview->replace('dev', (DEV) ? "DEV" : "");

		echo $this->superview->get();		
	}
	
	
	final protected function &viewport(){
		return $this->viewport;	
	}
	
	final protected function &superview(){
		return $this->superview;
	}
	
	final protected function &objects($type = ""){
		if($type == "")	return $this->objectStore;
		
		foreach($this->objectStore as $object){
			if(get_class($object) == $type){
				return $object;
			}
		}
		
		return false;	
	}
	
	final protected function setObject($type, &$newObj){
		foreach($this->objectStore as &$object){
			if(get_class($object) == $type){
				$object = $newObj;
			}
		}
		
	}
	
	final protected function &context(){
		return $this->context;
	}
	
	final protected function getUrl(){
		return BASEURL . str_replace("controller_", "", get_class($this)) . "/" . implode("/", $this->context);
	}
	
	final protected function redirect($location){
		$this->redirectTo = $location;
	}
	
	protected function noRender(){
		return false;
	}
	
	final public function __toString(){
		$this->renderWindow();
	}

}

?>