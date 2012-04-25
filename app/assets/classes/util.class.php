<?php
	class util {

		public static function selectTab(view &$superView, $selection){
			$tabs = array("home", "idea", "incubator", "project", "community", "resources");
			
			foreach($tabs as $tab){
				if($selection == $tab){
					$superView->replace($tab . '-selected', " selected");
				} else {
					$superView->replace($tab . '-selected', "");
				}
			}
			
		}
		
		public static function displayNewInnovators(){
			$output = "<h1>NEW INNOVATORS</h1>\n";
			
			$template = new view("frag.innovator");

			$users = new collection(collection::TYPE_USER);
			$users->setLimit(5);
			$users->setSort("id", collection::SORT_DESC);
			
			$userList = $users->get();
			
			foreach($userList as $innovator){
				$template->replace("name", $innovator->getHTMLName());
				$template->replace("tagline", $innovator->getTagline());
				
				$template->replace("src", $innovator->getPicture());
				
				$template->replace("img-size", 30);
				
				$output .= $template->get();
				
				$template->reset();
			}
			
			return $output;
		}
		
		public static function displayOpennessSections(){
		
			$template = new view("frag.opennessSections");
			$output = $template->get();
			
			return $output;
		}

		
		public static function flatButtons($buttons){
			$button = new view();
			$o = new view();
			$button->set("<a href=\"{url}\" class=\"flatButton\">{text}</a>");
			
			foreach($buttons as $text => $url){
				$button->replace("url", $url);
				$button->replace("text", $text);
				
				$o->append($button);
				
				$button->reset();
			}
			
			return $o;	
		}
		
		public static function userBox(user &$u, view &$superView){
			if($u->getId() != null){
				$superView->replace("userBox", "<div id=\"loggedIn\"><div id=\"user\" style=\"float:left\"><a href=\"/profile\"><img src='{$u->getPicture()}' width='30' height='30' align='left' alt='User profile picture' /> {$u->getName()}</a> (<a href=\"/auth/logout\">Logout</a>)</div></div>");			
			} else {
				$superView->replace("userBox", new view('frag.login'));
			}
		}
		
		public static function addScripts(array $scripts){
			$out = "";
			foreach($scripts as $script){
				$out .= util::newScript($script) . "\n";
			}
			
			return $out;
		}
		
		public static function newScript($name){
			return "<script type=\"text/javascript\" src=\"$name\"></script>";
		}
		
		public static function parseLinks($input){
			return preg_replace("/(https?:\/\/[a-zA-Z0-9_\-%\.\/]+)/i", "<a href=\"$1\" target=\"_blank\">$1</a>", $input);
		}

		public static function getCategories($selection = ""){
			// Get categories
			$collection = new collection(collection::TYPE_CATEGORY);
			$option = new view('frag.option');
			$output = new view();
			
			foreach($collection->get() as $category){
				$option->replace("val", $category->getId());
				$option->replace("text", $category->getName());
				$option->replace("id", "cat-" . $category->getId());
				
				if($selection != ""){
					if($selection->getId() == $category->getId()) {
						$option->replace("selection", "selected=\"selected\"");
					} else {
						$option->replace("selection", "");
					}
				}
				
				$output->append($option);
				$option->reset();
			}
			return $output;
		}
		
		public static function getLicense($selection = ""){
			// Get licenses
			$collection = new collection(collection::TYPE_LICENSE);
			$option = new view('frag.option');
			$output = new view();
			
			foreach($collection->get() as $license){
				$option->replace("val", $license->getId());
				$option->replace("text", $license->getName());
				$option->replace("id", "lic-" . $license->getId());
				
				if($selection != ""){
					if($selection->getId() == $license->getId()) {
						$option->replace("selection", "selected=\"selected\"");
					} else {
						$option->replace("selection", "");
					}
				}
				
				$output->append($option);
				$option->reset();
			}
			return $output;
		}
		
		public static function pass($p){
			return md5($p . "4565QQRGfdkJf%^");
		}
		
		public static function id($i){
			return $i;
		}

	}
?>