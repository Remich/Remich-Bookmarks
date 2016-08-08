<?php
	/**
	* class Config
	*
	* Contains configuration of the script and a method to return a certain value
	*/
	class Config  {
	
		private static $cfg = array(
			
			"dbhost" => "127.0.0.1",
			"dbname" => "rmis_1144",			// name of the database
			"dbuser" => "rmis_1144",			// username of the database
			"dbpass" => "rLswwGtGYp2etuhv",		// password for the database
			"dbport" => 3306,					// database port
			
			"title"  => "Remich Bookmarks",
			"items_per_page" => "30",
			"contact" => "foo@bar.de",

			"debug" => true
			
		);
		
		public static function getOption($key){
			return self::$cfg[$key];
		}
	} 
?>
