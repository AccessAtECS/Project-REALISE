<?php
try {

	// Start the application
	require_once("app/bootstrap.php");
	
	$request = isset($_GET['p']) && $_GET['p'] != "/" ? $_GET['p'] : SYS_DEFAULTCNTRLR;
	
	// Generate the superview.
	$superview = new view('superview');
	
	// Create a new despatcher object.
	$despatcher = new despatcher(function_exists('getRuntimeObjects') ? getRuntimeObjects() : array());

	// Set the superview
	$despatcher->setSuperview($superview);
	$despatcher->request($request);
	
} catch(Exception $e){
	
	// Try and use a builtin handler.
	$Vault = Vault::singleton();
	$handler = $Vault->objects("exceptionHandler");

	switch( $e->getCode() ){ 
	 	case 404:
			// Page is not found, load the default controller.
			$despatcher->setSuperview($superview);
			$despacher->request(SYS_DEFAULTCNTRLR . "/" . $request);
		break;
		
		default:
			echo $e->getMessage();
			exit;
		break;	
	}
}

?>