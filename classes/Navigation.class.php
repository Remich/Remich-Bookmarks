<?php

	require_once('classes/View.class.php');
	
	/***
	* class Navigation
	*
	* This class loads the Navigation 
	*/
	class Navigation {
		
		// path to the modules  
		private $path = 'modules';
		// available modules
		private $modules = array();
		// parsed modules
		private $modulesCfg = array();
		// navigation string
		private $navigation = null;
		
		public function __construct( $module = null ) {
		
			if($module == null) {
		
				$this->getModules();
				$this->getModulesConfig();
				$order = $this->validateOptionNavigationOrder();
			
				// rearrange available modules according to the order in the configuration
				$ar = $this->modules; 
				foreach($order as $key => $item)
					$this->modules[$item] = $ar[$key];
				
			} else
				$this->modules = array($module);
			
			$this->getNavigation();
			
		}
		
		public function load() {
		
			return $this->navigation;
		
		}
		
		public function getScripts() {
		
			$scripts = array();
			foreach($this->modulesCfg as $item)
				if(isset($item['config']['scripts'])) 
					foreach($item['config']['scripts'] as $item2)
						$scripts[] = $item2;
			
			return $scripts;
		
		}
		
		public function getStyles() {
		
			$styles = array();
			foreach($this->modulesCfg as $item)
				if(isset($item['config']['styles'])) 
					foreach($item['config']['styles'] as $item2)
						$styles[] = $item2;
			
			return $styles;
		
		}
		
		private function getNavigation() {
			
			foreach($this->modules as $item) {
				$this->navigation .= $this->runController($item);
				/*$link = new View($item);
				$link = setTemplate('navigation');
				$this->navigation .= $link->loadTemplate();*/
			}
			
		}
		
		private function runController($module) {
		
			require_once($this->path.DIRECTORY_SEPARATOR.'Controller.'.$module.'.inc.php');
			
			$eval = '$controller = new '.$module.'( array(\'module\' => $module, \'page\' => \'navigation\',  \'wrapping\' => 0) );';
			eval($eval);
			return $controller->control();			
		
		}
		
		private function getFiles() {
		
			$ar = array();
			
			if(@$handle = opendir($this->path)) {
			
				while (false !== ($file = readdir($handle)))
					if ($file != "." && $file != "..") $ar[] = $file;
					
				closedir($handle);
				
				return $ar;
			
			} else die('Error: Could not open path "' . $this->path . '"');
		
		}
		
		private function getModules() {
			
			$files = $this->getFiles();
			
			foreach($files as $key => $item)
				if(!is_dir($this->path . DIRECTORY_SEPARATOR . $item))
					unset($files[$key]);
					
			$this->modules = $files;		
		
		}
		
		private function getModulesConfig() {
		
			foreach($this->modules as $key => $item) {
				
				if(!include($this->path . DIRECTORY_SEPARATOR . $item . '/Config.inc.php'))
					die('Error: Could not load "Config.inc.php" of Module "' . $item . '"');
			
				$this->modulesCfg[$key]['name'] = $item;
				$this->modulesCfg[$key]['config'] = $cfg;
			
			}
		
		}
		
		private function validateOptionDefault() {
			
			$default = 0;
			foreach($this->modulesCfg as $key => $item) {
						
				if($item['config']['default'] == 1 && $default == 0) $default = 1;
				elseif($item['config']['default'] == 1 && $default == 1)
					die('Error: Only one default module allowed');
			
			}
		
		}
		
		private function validateOptionNavigationOrder() {
		
			$ar = array();
			
			foreach($this->modulesCfg as $key => $item) {
						
				if(!isset($item['config']['navigation_number'])) 
					die('Error in module '.$this->modules[$key].': Configuration "navigation_order" not set!');
					
				if(in_array($item['config']['navigation_number'], $ar))
					die('Error in module '.$this->modules[$key].': Value of Configuration "navigation_order" already taken!');
				
				$ar[$key] = $item['config']['navigation_number'];
			
			}
			
			return $ar;
		
		}
		
	}  // <!-- end class ’Navigation’ -->   
?>
