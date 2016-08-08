<?php
	
	/**
	* class Misc
	*
	* miscellaneous functions
	*/
	class Misc {
	
		// preformatted data
		public static function pre($var) {
		
			echo "<pre>";
			print_r($var);
			echo "</pre>";
			
		}
		public static function dump($var) {
			echo "<pre>";
			var_dump($var);
			echo "</pre>";
		}
	
		public static function shortenStr($string, $length, $wordCut = 1) {
	
			if(strlen($string) > $length) {
		
				$string = substr($string,0,$length)."…";
				
				if($wordCut) {
				
					$string_ende = strrchr($string, " ");
					$string = str_replace($string_ende,"…", $string);
					
				}
				
			} return $string;
		}
		
		public static function returnBytes($val) {
		
			$val = trim($val);
			$last = strtolower($val[strlen($val)-1]);
			
			switch($last) {
		
				case 'g': $val *= 1024;
				case 'm': $val *= 1024;
				case 'k': $val *= 1024;
				
			} return $val;
			
		}
		
		public static function in_arrayi($needle, $haystack) {
		
		    return in_array(strtolower($needle), array_map('strtolower', $haystack));
		    
		}
		
		// tags LIKE: filter wrong results and return new filter string to add to qury
		public static function filterWrongResults($query, $tag, $caseInsensitive = NULL) {
	
			$data = DB::Get($query);
			$wrong_items = array();
	
			foreach($data as $key => $item) {

				$tags = explode('#', $item['tags']);
				
				if($caseInsensitive == NULL) {
					if(!in_array($tag, $tags))
						$wrong_items[] = $item['id'];
				} else
					if(!Misc::in_arrayi($tag, $tags))
						$wrong_items[] = $item['id'];
			
			}

			$str = '';
			foreach($wrong_items as $item) $str .= ' AND id != '.$item;
	
			return $str;
	
		}
		
		public static function getOption($table, $needle, $where) {
		
			foreach($where as $key => $item)
				@$str .= $key.' = '.$item.' ';
		
			$query = 'SELECT
						'.$needle.'
					  FROM
					  	'.$table.'
					  WHERE
					  	'.$str.'
					  LIMIT 1';
			$data = DB::getOne($query);
			
			return $data[$needle];
		
		}
		
		public static function getRow($table, $where) {
		
			foreach($where as $key => $item)
				@$str .= $key.' = '.$item.' ';
		
			$query = 'SELECT
						*
					  FROM
					  	'.$table.'
					  WHERE
					  	'.$str.'
					  LIMIT 1';
			$data = DB::getOne($query);
			
			return $data;
		
		}
		
		public static function getImage($url, $hash, $folder, $output = false) {
	
			if(!is_dir($folder))
				mkdir($folder, 0777);
		
			$file =  $folder.$hash.'.png';
		
			$path = Url::getCurrentServerPath();
			// $script = $path.'extensions/wkhtmltoimage-amd64';
			$script = 'wkhtmltoimage';

			
			$command = 'timeout 60s xvfb-run --server-args="-screen 0, 1120x700x24" '.escapeshellarg($script).' --use-xserver --width 1120 --height 700 --stop-slow-scripts --encoding utf8 --format png --disable-smart-width --quality 100 '.escapeshellarg($url).' '.escapeshellarg($file).' > /dev/null';

			// if($output)
			echo '<br>Running Command: '.$command.' …<br><br>';
	
			$last_line = system($command, $output);
		
			if($output)
			echo '<br><br>Command returned: ' . $output.'<br>';
		
			if(file_exists($file)) {
				if($output)
				echo '<br>Creating Thumbnail…';
				$src = imagecreatefrompng($file);
				$dst = imagecreatetruecolor(160, 100);
				imagecopyresampled( $dst , $src , 0 , 0 , 0 , 0 , 160 , 100 , 1120 , 700);
				imagepng($dst, $file, 0);
				imagedestroy($dst);
			}
	
		}

		public static function hasOnlyHiddenItems($tid) {
						
			// Select all Items whith this $tag and status hidden=1
			$query = 'SELECT * FROM rel_bookmarks_tags JOIN bookmark_items ON rel_bookmarks_tags.id_a = bookmark_items.id AND rel_bookmarks_tags.id_b = :tid AND bookmark_items.hidden = 1';
			$params = array(
				':tid' => $tid
			);
			
			$data1 = DB::get($query, $params);
		
			// Select all Items with this $tag
			$query = 'SELECT * FROM rel_bookmarks_tags JOIN bookmark_items ON rel_bookmarks_tags.id_a = bookmark_items.id AND rel_bookmarks_tags.id_b = :tid';
			$params = array(
				':tid' => $tid
			);
			
			$data2 = DB::get($query, $params);
		
			if( sizeof($data1) != sizeof($data2) || sizeof($data1) == 0 && sizeof($data2) == 0 )
				return 0;
			else return 1;
					
		}

		public static function hasRight($id, $table) {
			switch($table) {
				case 'bookmark_items':
					$query = 'SELECT uid FROM bookmark_items WHERE id = :id LIMIT 1';
				break;

				case 'bookmark_tags':
					$query = 'SELECT uid FROM bookmark_tags WHERE id = :id LIMIT 1';
				break;
			}

			$params = array(':id' => $id);
			$data = DB::getOne($query, $params);

			if(sizeof($data) === 0) {
				die('Software-Error: Wrong ID supplied. No Data found.');
			}

			if($data['uid'] !== $_SESSION['uid']) {
				return false;
			} else {
				return true;
			}
		}
	
	} // <!-- end class ’Misc’ -->
	
?>
