<?php

	require_once('classes/Foo.class.php');
	require_once('classes/Qualify.class.php');
	require_once('classes/DB.class.php');
	require_once('classes/Url.class.php');
	require_once('classes/Misc.class.php');
	require_once('classes/Auth.class.php');
	
	class Universal extends Foo {
	
		public $request = null; // our request-object; we store here the data from $_POST, $_GET and $_FILES
		
		/**
		* Constructor
		*
		* @param Array $request, merged array of $_GET & $_POST & $_FILES
		*/
		public function __construct($request) {
		
			$this->request = $request;
			
			// Due to the use of jQuery.serialize() the POST data is being transferred in a plain string, so we have to manually make an array out of it:
			if(@$this->request['post'])	$this->request['post'] = Qualify::StringToPost($this->request['post']);
			
		} // <!-- end function ’__construct()’ -->
		
		/**
		* Running the actual application
		*/
		public function control() {
		
			switch(@$this->request['page']) {
			
				default:
				
					die('default. This is the place where nothing happens');
			
				break; // <!-- end ’default’ -->
				
				case 'login':
					
					$this->isInRequest( array('username', 'password') );
					
					die( Auth::login($this->request['username'], $this->request['password']) );		
				
				break; // <!-- end case ’login’ -->
				
				case 'logout':
				
					Auth::logout();
					header('Location: index.php');
				
				break; // <!-- end case ’logout’ -->
				
				case 'updateSessionUrl':
				
					$this->isInRequest('url');
					
					$url = urldecode($this->request['url']);
					$aUrl = Url::explode($url);
					
					$_SESSION['url_'.$aUrl['query_params']['module']] = $url;
					die('1');
				
				break; // <!-- end case ’updateSessionUrl’ -->
				
				case 'urlHasParams':
					
					$this->isInRequest('windowLocation');
					
					$url = Url::explode($this->request['windowLocation']);
					if(sizeof($url['query_params']) > 0)
						die('1');
					else
						die('0');
				
				break; // <!-- end case ’urlHarParams’ -->
				
				case 'changesUrlModule':
					
					$this->isInRequest( array('windowLocation', 'origin') );
					
					$origin = Url::explode($this->request['origin']);
					$url = Url::explode($this->request['windowLocation']);
					
					if(sizeof($origin['query_params']) > 0) {
					
						if($origin['query_params']['module'] != $url['query_params']['module']) {
						
							die( Url::updateParams( $this->request['windowLocation'], array('page' => 'menu', 'wrapping' => '0') ) );
						
						} else die('0');
						
					} else die('0');
				
				break; // <!-- end case ’changesUrlModule’ -->

				case 'register_2':

					$this->isInRequest( array( 'username', 'mail', 'password' ) );

					// Check if username or mail already present
					$query = 'SELECT COUNT(*) as no 
								FROM users
								WHERE username = :username
									OR mail = :mail
								LIMIT 1';
					$params = array(
						':username' => $this->request['username'],
						':mail' => $this->request['mail']
					);
					$data = DB::getOne($query, $params);

					if($data['no'] > 0) {
						die('Error: Username or Email already taken');
					}

					// Insert
					$query = 'INSERT INTO users (username, mail, password, userhash) VALUE(:username, :mail, :password, :userhash)';
					$params[':password'] = $this->request['password'];
					$params[':userhash'] = hash("sha256", $this->request['username'] );

					DB::execute($query, $params);

					die('1');

				break;
					
				
			} // <!-- end ’switch(@$this->request['page'])’ -->
		
		} // <!-- end function ’control()’ -->
		
		/**
		* Displaying the content
		*
		* @return String, the generated html code
		*/
		public function display() {
		} // <!-- end function ’display()’ -->
		
	} // <!-- end class ’Controller’ -->   
?>
