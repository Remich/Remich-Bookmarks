<?php
	
	class Relation {
		
		protected $_id;
		protected $_name = "Relation";
		protected $_table = "";
		protected $_id_a = null;
		protected $_id_b = null;
		
		/**
		* Constructor
		*
		*/
		function __construct($table, $id_a, $id_b) {
			$this->_table = $table;
			$this->_id_a = $id_a;
			$this->_id_b = $id_b;			

			$query = 'SELECT * FROM '.$this->_table.' WHERE id_a = :id_a AND id_b = :id_b';
			$this->_data = DB::getOne($query, array(':id_a' => $this->_id_a, ':id_b' => $this->_id_b));
			
			if(sizeof($this->_data)==0)
				$this->newEntry(); 
		} 
		public function setTable($table) {
			$this->_table = $table;
		}
		
		public function newEntry() {
			$query = 'INSERT INTO 
						'.$this->_table.' (id_a, id_b) 
					  VALUES
						(:id_a, :id_b)';
			$params = array(
				':id_a'=>$this->_id_a,
				':id_b'=>$this->_id_b
			);
			DB::execute($query, $params);
		}
		
		public function delete() {
			$query = 'DELETE FROM '.$this->_table.' WHERE id_a = :id_a AND id_b = :id_b';
			$params = array(':id_a'=>$this->_id_a, ':id_b'=>$this->_id_b);
			DB::execute($query, $params);
		}

	} // <!-- end class ’Controller’ -->
?>
