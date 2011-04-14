<?php
try {

	// Start the application
	require_once("app/bootstrap.php");
	
	$request = isset($_GET['p']) && $_GET['p'] != "/" ? $_GET['p'] : INSTEP_SYS_DEFAULTCNTRLR;
	
	// Generate the superview.
	$superview = new view('superview');
	
	// Create a new despatcher object.
	$despacher = new despatcher(function_exists('getRuntimeObjects') ? getRuntimeObjects() : array());

	// Set the superview
	$despacher->setSuperview($superview);
	$despacher->request($request);
	
} catch(Exception $e){
	switch( $e->getCode() ){ 
	 	case 404:
			// Page is not found, load the default controller.
			$despacher->setSuperview($superview);
			$despacher->request(INSTEP_SYS_DEFAULTCNTRLR . "/" . $request);
		break;
		
		default:
			echo $e->getMessage();
			exit;
		break;	
	}
}

?>