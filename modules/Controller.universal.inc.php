<?php
	
	require_once('classes/Foo.class.php');
	require_once('classes/Qualify.class.php');
	require_once('classes/DB.class.php');
	require_once('classes/View.class.php');
	
	class Universal extends Foo {
	
		public $request = null; // our request-object; we store here the data from $_POST, $_GET and $_FILES
		private $view = null; // our view object
		
		/**
		* Constructor
		*
		* @param Array $request, merged array of $_GET & $_POST & $_FILES
		*/
		public function __construct($request) {
		
			$this->request = $request;
			$this->view = new View();
			
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


				case 'register':
					$this->view->setTemplate('register');
				break;

				
			} // <!-- end ’switch(@$this->request['page'])’ -->
		
		} // <!-- end function ’control()’ -->
		
		/**
		* Displaying the content
		*
		* @return String, the generated html code
		*/
		public function display() {
			return $this->view->loadTemplate();
		} // <!-- end function ’display()’ -->
		
	} // <!-- end class ’Controller’ -->   
?>
