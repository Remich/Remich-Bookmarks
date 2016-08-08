<?php

	class TagCloud {
		
		private $_tags = null;
		private $_min = 9; // Minimum Fontsize
		private $_max = 94; // Maximum Fontsize
		private $_table_relation = null;
		private $_table_tags = null;
		private $_tag_id = null;
		private $_page = null;
		private $_uid = null;
		
		public function setTableRelation($table = null) {
			if($table == null)
				die('Error: Nullreference Table Relation');
			
			$this->_table_relation = $table;			
		}
		public function setTableTags($table = null) {
			if($table == null)
				die('Error: Nullreference Table Relation');
				
			$this->_table_tags = $table;
		}
		public function setPage($page = null) {
			if($page == null)
				die('Error: Nullreference Table Relation');
		
			$this->_page = $page;
		}
		public function setFontMax($max) {
			$this->_max = $max;
		}
		public function setFontMin($min) {
			$this->_min = $min ;
		}
		public function setUID($uid) {
			$this->_uid = $uid;
		}
		public function __construct($tag_id = null) {
			$this->_tag_id = $tag_id;		
		}
		
		public function generate() {
			if($this->_tag_id == null) {
				if($this->_uid === null) {
					die('Error: Tagcloud.class.php on line 52: No UID set!');
				}
				$query = 'SELECT * FROM '.$this->_table_tags.' WHERE uid = :uid ORDER BY name ASC';
				$params = array(
					':uid' => $this->_uid
				);
				$this->_tags = DB::get($query, $params);
			} else {
				$params = array(":tid" => $this->_tag_id);
				
				//TODO: in TagManager auslagern
				
				// alle items mit tag_id tag holen
				$tags = explode(".", $this->_tag_id);
				$a_ids = array();
				foreach($tags as $item) {
					$query = 'SELECT id_a FROM '.$this->_table_relation.' WHERE id_b = :id_b';
					$tmp = DB::get($query, array(':id_b'=>$item));
					foreach($tmp as $item2)
						$a_ids[$item][] = $item2['id_a'];
				}
				if(sizeof($a_ids)>1)
					$result = call_user_func_array('array_intersect',$a_ids);
				else
					foreach($a_ids as $item)
						$result = $item;
					
				// alle tags von den items holen
				$tags = array();
				foreach($result as $item) {
					$query = 'SELECT
								id_b
							  FROM
								'.$this->_table_relation.'
							  WHERE
								id_a = :id_a';
					$params = array(':id_a' => $item);
					$data = DB::Get($query, $params);
					foreach($data as $key => $item) {
						$tags[] = $item['id_b'];
					}
				}

			
				// Remove duplicates
				$tags = array_unique($tags);

				
				// Remove parent tag
				foreach($tags as $key => $item)
					if($item == $this->_tag_id) unset($tags[$key]);
			
				// restliche tag informationen holen
				foreach($tags as $key => $item) {
					$query = "SELECT
								*
							  FROM
								".$this->_table_tags."
							  WHERE
								id = :cid";
					$tags[$key] = DB::getOne($query, array(':cid' => $item));
				}
				$this->_tags = $tags;
			}
			
			// Remove currently viewed tags
			$tags2 = explode(".", $this->_tag_id);
			foreach($this->_tags as $key => $item)
				foreach($tags2 as $key2 => $item2)
					if($item['id'] == $item2)
						unset($this->_tags[$key]);
			
				
			foreach($this->_tags as $key => $item) {
				$query = 'SELECT
							COUNT(*) as quantity
						  FROM
							'.$this->_table_relation.'
						  WHERE
							id_b = :id_b LIMIT 1';
				$data = DB::getOne($query, array(':id_b' => $item['id']));
				$this->_tags[$key]['occurences'] = $data['quantity'];
			}

			// Don't display tags with only hidden files
			foreach($this->_tags as $key => $item)
				if(Misc::hasOnlyHiddenItems($item['id']))
					if(!isset($_SESSION['hidden']))
						unset($this->_tags[$key]);
					else
						$this->_tags[$key]['hidden'] = 1;
			
		}
			
		
		public function display($foo = null) {
			if( count($this->_tags) == 0) {	
				return "Nicht genug Daten für die TagCloud";
			} else {
				$most = 1;
				foreach($this->_tags as $item)
					if($item['occurences'] > $most)
						$most = $item['occurences'];
					
				$mosthits = 1;
				foreach($this->_tags as $item)
					if($item['hits'] > $mosthits)
						$mosthits = $item['hits'];
		
				$tags = array();
				foreach($this->_tags as $key => $item) {						
					$font = ceil(  ($item['occurences'] / $most ) * $this->_max );
					$color = ceil(($item['hits'] / $mosthits ) * 255);
					if($color < 100 && $color != 0) $color = 100;

					$color = ceil( ($item['hits'] / ($mosthits / 32)) * 255);
					// echo $color."<br>";
					if($color > 255) $color = 255;
				
					$half = dechex(ceil(ceil(  ($item['hits'] / $mosthits ) * 255) / 2.5));
					if($font < $this->_min)	$font = $this->_min;
					$tags[] = ' <a style="margin-right: 15px; font-size:'.$font.'px; color:#'.dechex($color).(dechex($color/2)).'00 !important" href="index.php?module=bookmarks&ajax=1&page=load&id=filter_tag&tag='.urlencode($item['id']).'" title="'.$item['occurences'].' Entries" target="content" class="url '. (@$item['hidden'] === 1 ? "clHidden" : "")  .' ">'.$item['name'].'</a>';
				}
				return '<p class="tags">' . implode("", $tags) . "</p>";
			}
		}
	} 
?>
