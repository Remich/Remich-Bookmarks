<?php

	class Tag extends ModelSingle {
		
		protected $_id;
		protected $_name = "Tag";
		protected $_table = "";
		
		/**
		* Constructor
		*
		*/
		function __construct($array=null) {
			
			if(isset($array['table']))
				$this->_table = $array['table'];
			if(isset($array['id'])) {
				$this->_id = $array['id'];
				$query = 'SELECT * FROM '.$this->_table.' WHERE id = :id';
				$this->_data = DB::getOne($query, array(':id' => $this->_id));
			}		
			if(isset($array['name'])) {
				$query = 'SELECT * FROM '.$this->_table.' WHERE name = :name';		
				$this->_data = DB::getOne($query, array(':name' => $array['name']));
				if(sizeof($this->_data)==0) {
					$this->_data['name'] = $array['name'];
					$this->newEntry();
				}
			}
			
		}
		
		private function newEntry() {
			$query = 'INSERT INTO '.$this->_table.'
						 (uid, name)
				      VALUES
				          (:uid, :name)';
			$params = array(
				':name' => (isset($this->_data['name'])?$this->_data['name']:"Default Tagname"),
				':uid' => 1
			);
			DB::execute($query, $params);
			$lastID = DB::lastId('id');
			$this->load($lastID);			
		}
		public function setTable($table) {
			$this->_table = $table;
		}
		
		public function getName() {
			return $this->_data['name'];
		}
		public function getId() {
			return $this->_data['id'];
		}
		
		public function getRelations($table) {
			$query = 'SELECT * FROM '.$table.' WHERE id_b = :id_b';
			return  DB::get($query, array(':id_b' => $this->_id));
		}
		
		public function delete() {
			$query = 'DELETE FROM '.$this->_table.' WHERE id = :id';
			$params = array(':id'=>$this->_id);
			DB::execute($query, $params);
		}
		
		public function save() {
			$query = 'UPDATE '.$this->_table.' SET 
						name=:n_value
					  WHERE id=:id';
			$params = array(
				':n_value' => $this->_data['name'],
				':id' => $this->_data['id']);
			DB::execute($query, $params);
		}
		
		public function load($id) {
			$query = 'SELECT *
					  FROM '.$this->_table.'
					  WHERE id = :id';
				
			$this->_data = DB::getOne($query, array(':id' => $id));
	
			if(sizeof($this->_data) == 0)
				$this->set(  array(
						'id' => $id,
						'title' => "Fehler: Tag mit id ".$id." nicht gefunden"
				) );
		
			$options = array('htmlspecialchars', 'utf8_decode', 'stripslashes');
			Sanitize::process_array($this->_data, $options);
		}
		public function incHit() {
			$query = 'UPDATE '.$this->_table.' SET hits = :hits WHERE id = :id';
			$params = array(':hits' => ++$this->_data['hits'], ':id' => $this->_data['id']);
			DB::execute($query, $params);
		}

	} // <!-- end class ’Controller’ -->
?>
