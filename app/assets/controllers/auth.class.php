<?php

class controller_auth extends controller {

	public function renderViewport(){
	
		$this->bind("linkedin", "linkedinlogin");
		$this->bind("local", "locallogin");
		$this->bind("register", "localRegistration");
		$this->bind("logout", "logout");
		$this->bind("verify", "verify");
		$this->bindDefault('invalidResponse');	
	
	}

	
	protected function locallogin(){
		try {
			$u = new user($_POST, user::LOCAL_ARRAY);
			
			$this->setObject(get_class($u), $u);
	
			$this->redirect("/home");			
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	protected function localRegistration(){
		
		// Check login
		try {
			$u = new user($_POST['username'], user::USERNAME_CHECK);
			
			$u->setName($_POST['name']);
			$u->setTagline("");
			$u->setEmail($_POST['email']);
			
			$u->setUsername($_POST['username']);
			$u->setHash(util::pass($_POST['password']));
			
			$gravatar = "http://www.gravatar.com/avatar/" . md5( strtolower(trim($_POST['email'])) ) . "?d=" . urlencode(INSTEP_BASEURL . "presentation/images/avatar.png") . "&s=60";
			
			$u->setPicture($gravatar);
			$u->commit();
			
			$this->setObject(get_class($u), $u);
	
			$this->redirect("/home");			
			
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	protected function linkedinlogin(){
		$linkedIn = new linkedin('tia2biBzgu20ST0XwYSuG-a4ciAcBo8UsX6IzzKYjlDmFs1h0o8w_W7r1mG07n6U', 'pKL9nJh9Tw3LZPeSHQ3j2IcptRPDkTsA1u9PPFSIIEwwZK1HI4IcaUQoFkpkKRiG', INSTEP_BASEURL . "auth/verify");
		
		//$linkedIn->debug = true;
		
		$linkedIn->getRequestToken();
		
		$_SESSION['LI_authObject'] = $linkedIn;
		
		$this->redirect($linkedIn->generateAuthorizeUrl()); 
		
	}
	
	protected function verify(){
				
		if(isset($_GET['oauth_problem'])){
			// Authentication problem.
			switch($_GET['oauth_problem']){
			
				case "user_refused":
					echo "Your username and password did not match for linkedin. <a href='/home'>Return to the homepage</a>";
				break;
				
				default:
					echo "Linkedin reported an OAuth error: " . $_GET['oauth_problem'];
				break;
			}
		}

		// Unserialize the object from session store.
		$linkedIn = $_SESSION['LI_authObject'];
		
		if(get_class($linkedIn) != "linkedin") throw new Exception("Unserialised linkedin object does not match!");
		
		if(isset($_GET['oauth_verifier']) == false) throw new Exception("No OAuth token received.");
		
		$linkedIn->getAccessToken($_GET['oauth_verifier']);
		
		// Authenticated.
		
		// Get information about the logged in user.
		$xml_response = $linkedIn->getProfile("~:(id,first-name,last-name,headline,picture-url)");

		$person = new SimpleXMLElement($xml_response);
		
		$p = $person->xpath('child::node()'); 
		$p = (array)$p[0];

		$p['picture-url'] = isset($p['picture-url']) ? $p['picture-url'] : "";

		// Data parsed. Check for existance of user in system (else we'll create).
		try {
			$u = new user($p['id'], user::ID_LINKEDIN);
			
			if($p['picture-url'] != "" && $u->getPicture() == ""){
				$u->setPicture($p['picture-url']);
				$u->commit();
			}
			
		} catch(Exception $e){
			$u = new user();
			$u->setName($p['first-name'] . " " . $p['last-name']);
			$u->setTagline($p['headline']);
			$u->setLinkedinId($p['id']);
			if($p['picture-url'] != "") $u->setPicture($p['picture-url']);
			$u->commit();
		}

		// Write the user object to the system
		$this->setObject(get_class($u), $u);

		$this->redirect("/home");

	}
	
	protected function logout(){
		$u = new user();
		$this->setObject(get_class($u), $u);
		
		$this->redirect("/home");
	}
	
	protected function invalidResponse(){
		$this->redirect("/home");
	}
	
	protected function noRender(){
		return true;
	}

}

?>