<?php

class despatcher {

	private $controller;
	private $superView;
	private $objects;
	
	private $dataType;
	
	function __construct(array $objects = array() ){
		$this->objects = $objects;
		session_start();
	}

	public function setSuperview(view $superview){
		$this->superView = $superview;
		$this->superView->setIdentifier('superView');
	}
	
	public function request($request){	
		// Get the route
		$route = $this->describeRoute( $request );
		
		// Identify datatype		
		$this->setDataType($route);

		// Create the controller		
		$this->createController( $route['controller'], $route['context'], array_merge($this->objects, array(&$this->superView)) );		
	}
	
	private function setDataType(&$route){		
		if(count($route['context']) > 1){
			$routeEnd = array_slice($route['context'], -1);
			
			if(in_array($routeEnd[0], unserialize(INSTEP_SYS_RESTFORMATS))){
				$this->dataType = $routeEnd;
				$route['context'] = array_slice($route['context'], 0, -1);
			} else {
				$this->dataType = "view";
			}
		} else {
			$this->dataType = "view";
		}
	}
	
	private function createController($controllerName, $context, array $dependencies = array()){
		$path = INSTEP_SYS_ASSETDIR . "controllers/" . $controllerName . ".class.php";
		
		if(file_exists($path)){
			require_once($path);
			// Load the controller and set it as the controller.
			$c = "controller_$controllerName";
			$this->controller = new $c();

			// The controller MUST implement viewController to be allowed to interact with the main system.
			if(in_array("controller", class_parents($this->controller)) == false || in_array("viewController", class_implements($this->controller)) == false) throw new Exception("Controller exception: Requested controller '" . $controllerName . "' does not extend controller.", 509);
			
			
			// Initialise the controller
			$this->controller->init($context, $dependencies);
			
			$this->controller->render($this->dataType);
		} else {
			throw new Exception("Controller exception: Requested controller '" . $controllerName . "' does not exist.", 404);
		}	
	}

	private function describeRoute($request){
		preg_match_all("([^//]+)", $request, $matches);
		if(count($matches[0]) > 0){
			$request = array(
				"controller" => $matches[0][0],
				"context" => array_slice($matches[0], 1)
			);
		} else {
			$request = array(
				"controller" => INSTEP_SYS_DEFAULTCNTRLR,
				"context" => array()
			);		
		}
		return $request;
	}

}

?>