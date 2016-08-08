<?php

require_once("interfaces/iModel.interface.php");

class ModelSingle extends Model implements iModel {

    /**
     * Return HTML output
     */
    public function display() {
    	
        $tpl = new View ();
        $tpl->setTemplate($this->_template);
        $tpl->setTemplateDir($this->_templateDir.$this->_name."/views/");
        $tpl->assign('data', array( $this->_data ));
        return $tpl->loadTemplate();    

    }

} 