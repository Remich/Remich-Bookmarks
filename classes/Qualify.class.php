<?php

	/**
	* class Qualify
	*
	* Some methods to manipulate data
	*/
	class Qualify {

		public static function process_array($array, $function) {
			array_walk_recursive($array, $function);
			return $array;
		}
		
		public static function magic_quotes($string) {
			if (get_magic_quotes_gpc()) {
				$string = stripslashes($string);
			}
			return $string;
		}
		
		public static function make_save_str_in(&$string) {
			$string = Qualify::magic_quotes($string);
			$string = addslashes(stripslashes($string)); // stripslashes to avoid double slashing
			$string = htmlspecialchars($string);
			$string = trim($string);
			return $string;
		}

		public static function MaskArray(&$Array) {
			return Qualify::process_array($Array, Qualify::make_save_str_out2());
			// return $Array = Qualify::make_save_str_out2($Array);
		}
		
		public static function make_save_str_out(&$string) {
			$string = stripslashes($string);
			return $string;
		}
		
		// Alias for make_save_str_out2
		public static function MaskString(&$string) {
            $string = utf8_encode($string);
            // $string = strip_tags($string);
            $string = htmlspecialchars($string, ENT_QUOTES);
            $string = stripslashes($string);
            return $string;
		}
		
		public static function make_save_str_out2(&$string) {
            $string = utf8_encode($string);
            $string = htmlspecialchars($string, ENT_QUOTES);
            $string = strip_tags($string);
            // $string = htmlentities($string);
            $string = stripslashes($string);
            return $string;
        } 
        
        public static function make_save_str_out3(&$string) {
			$string = stripslashes($string);
			$string = utf8_encode($string);
			return $string;
		}
		
		public static function StringToPost($string){        
            $array = explode('&', $string);
            foreach($array as $key => $item) {
                $single = explode("=", $item);
                $array[$single[0]] = urldecode($single[1]);
                unset($array[$key]);
            }
            return $array;
        }
		
	}  // <!-- end class ’Qualify’ -->
	
?>
