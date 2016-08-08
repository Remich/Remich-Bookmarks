<?php

    class Model {
    	
        protected $_data = null;
        protected $_request = null;
        protected $_template = "index";
        protected $_templateDir = "models/";
        protected $_name = "UnnamedModel";
        protected $_id;
        protected $_table;
        
        public function __construct($array = null) {
        	if(isset($array['table']))
        		$this->_table = $array['table'];
        	if(isset($array['id']))
        		$this->_id = $array['id'];
        }
        
        public function set($array) {
        	foreach($array as $key => $item)
        		$this->_data[$key] = $item;
        }
        public function setTemplate($tpl) {
        	$this->_template = $tpl;
        }

        public function getTags($table) {
        	$query = 'SELECT * FROM '.$table.' WHERE id_a = :id_a';
        	return  DB::get($query, array(':id_a' => $this->_id));      
        }
    
    }

?>
