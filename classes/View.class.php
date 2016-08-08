<?php
	
	/***
	* class View
	*
	* This is the Template Class.
	* Providing methods to pass data from the Controller to the View and to load templates
	*/
	class View {
		
		// Contains the data, which shall be embedded in the template 
		private $_ = array();
		
		public function __construct() {
			
		}
		
		// Method to assign data to the template
		public function assign($key, $value) {
		
			$this->_[$key] = $value;
			
		}
		
		// Setting the name of the template
		public function setTemplate($template = 'index') {
		
			$this->template = $template;
			
		}
		
		/**
		* Load and return the template file
		*
		* @return string, Ouput of template
		*/
		public function loadTemplate(){
		
			// Creating path to template file & check if template exists
			$file = 'modules/templates/' . $this->template . '.php';
			$exists = file_exists($file); 
			
			if ($exists) {
			
				// The Output of the script is being stored in a buffer
				ob_start();
				
				include $file;
				$output = ob_get_contents();
				ob_end_clean();
				
				return $output;
				
			} else {
			
				return 'Error: Could not find template: '.$file;
				
			}
			
		}  // <!-- end function ’loadTemplate()’ -->
		
	}  // <!-- end class ’View’ -->   
?>
