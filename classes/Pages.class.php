<?php

	/**
	* class Pages
	*
	* Divides Content by displaying content with flipping function
	*/
	class Pages {
	
		var $_entrys;
		var $_itemsPerPage;
		private $module;
		
		public function __construct($entrys, $itemsPerPage, $module = NULL) {
			$this->_entrys = $entrys;
			$this->_itemsPerPage = $itemsPerPage;
			if($module == NULL)
				$this->module = 'bookmarks';
			else
				$this->module = $module;
		}
		
		public function getPages() {
			$pages = ceil($this->_entrys / $this->_itemsPerPage);
			if($pages == 0)
				return 1;
			else
				return $pages;
		}
		
		public function getStart($page) {
			return ($this->getPages() - $page) * $this->_itemsPerPage;
		}
		
		public function getHtml($pageActive, $pages, $float = 0) {
			if($pageActive == 'all') {
				$pageActive = 0;
			}
			$pageActive_plus_1 = $pageActive + 1;
			$pageActive_minus_1 = $pageActive - 1;
			if($float == 1) {
				$class = ' right"';
			}
			$return = false;
			if(is_numeric($pageActive) AND $pages != 1) {
				$return = '<span class="pages'.@$class.'"><span>Pages: </span>';
				if($pageActive != $pages AND $pageActive != 0) {
					$return .= '<a href="index.php?module='.$this->module.'&ajax=1&page=load&id=page&jump='.$pageActive_plus_1.'" target="content" class="url rm_layout">&#171;</a> ';
				}
				
				if($pageActive != $pages) {
					$return .= '<a href="index.php?module='.$this->module.'&ajax=1&page=load&id=page&jump='.$pages.'" target="content" class="url rm_layout">'.$pages.'</a> ';
				} else {
					$return .= '<strong>'.$pages.'</strong> ';
				}
				
				for($a = $pages - 1; $a >= 2; $a--) {
					if($pageActive != $a)
						{
							if($a > $pageActive + 4 OR $a < $pageActive - 4) {
								if($a > $pageActive + 5 OR $a < $pageActive - 5) {
    						} else {
    							$return .= '&#133 ';
    						}
    					} else {
    						$return .= '<a href="index.php?module='.$this->module.'&ajax=1&page=load&id=page&jump='.$a.'" target="content" class="url rm_layout">'.$a.'</a> ';
    					}
    				} else{
    					$return .= '<strong>'.$a.'</strong> ';
    				}
    			}
    			if($pageActive != 1) {
    				$return .= '<a href="index.php?module='.$this->module.'&ajax=1&page=load&id=page&jump=1" target="content" class="url rm_layout">1</a> ';
    			} else {
    				$return .= '<strong>1</strong> ';
    			}
    			
    			if($pageActive != 1 AND $pageActive != 0) {
    				$return .= '<a href="index.php?module='.$this->module.'&ajax=1&page=load&id=page&jump='.$pageActive_minus_1.'" target="content" class="url rm_layout">&#187;</a> ';
    			}
    			if($pageActive == 0) {
    				$return .= '<strong>All</strong> </span>';
    			} else {
    				$return .= '<a href="index.php?module='.$this->module.'&ajax=1&page=load&id=page&jump=all" target="content" class="url rm_layout">All</a> </span>';
    			}
    		}
    		return $return;
    	}
	} // <!-- end class ’Pages’ -->
	
?>
