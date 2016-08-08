<?php

	class DB {
		
		private static $_db;	
		private static $_number_of_queries = 0;
		private static $_number_of_connections = 0;
		
		private static function getInstance() {
			if(!self::$_db) {
				try {
					self::$_db = new PDO(
							'mysql:host='.Config::getOption('dbhost').';'.
							'dbname='.Config::getOption('dbname').';'.
							'charset=utf8mb4',
							/*'port='.Config::getOption('dbport'),*/
							Config::getOption('dbuser'),
							Config::getOption('dbpass'),
							array(
									PDO::ATTR_PERSISTENT => false,
									PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ,
									PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
							)
					);
					self::$_number_of_connections++;
				}
				catch(Exception $e){
					if(Config::getOption('debug')) {
						Misc::dump($e);
						die();
					} else die($e->getMessage());					
				}
			}
			return self::$_db;
		}
		
		// Executing an INSERT/UPDATE/DELETE statement
		public static function execute($query, $params = NULL) {
			$stmt = self::getInstance()->prepare($query);
			try {
				$stmt->execute($params);
			}
			catch(Exception $e) {
				if(Config::getOption('debug')) {
					Misc::dump($e);
					die();
				} else die($e->getMessage());
			}
			
			self::$_number_of_queries++;
			return $stmt;
		}
		
		// Executing a SELECT statement with more than 1 expected result
		public static function get($query, $params = NULL) {
			try {
				$stmt = self::execute($query, $params);
			}
			catch(Exception $e) {
				if(Config::getOption('debug')) {
					Misc::dump($e);
					die();
				} else die($e->getMessage());				
			}	
			$data = array();
			foreach($stmt->fetchAll() as $row) $data[] = $row;
				
			return $data;
		}
		
		// Executing a SELECT statement with only 1 expected result
		public static function getOne($query, $params = NULL) {
			try{
				$stmt = self::execute($query, $params);
			}
			catch(Exception $e) {
				if(Config::getOption('debug')) {
					Misc::dump($e);
					die();
				} else die($e->getMessage());
			}
			$data = array();
			foreach($stmt->fetchAll() as $row)
				$data[] = $row;				

			return (sizeof($data) > 0) ? $data[0] : array();
		}
		
		// Executing a SELECT statement with more than 1 expected result
		public static function getSet($query, $params = NULL) {
			try {
				$stmt = self::execute($query, $params);
			}
			catch(Exception $e) {
				if(Config::getOption('debug')) {
					Misc::dump($e);
					die();
				} else die($e->getMessage());	
			}
		
			$data = array();
			foreach($stmt->fetchAll() as $row) $data[] = $row;
			$data = array_map("unserialize", array_unique(array_map("serialize", $data)));
			return $data; 
		}
		
		public static function lastId() {
			return self::getInstance()->lastInsertId('id');
		}
		
		public static function getNumberOfQueries() {
			return self::$_number_of_queries;
		}
		public static function getNumberOfConnections() {
			return self::$_number_of_connections;
		}
       
	}

?>
