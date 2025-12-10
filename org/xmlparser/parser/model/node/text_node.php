<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/model/node/node.php";

class TextNode extends Node{
    
    public function __construct($value){
        parent::__construct();
        $this -> value = $value;
    }
	
	public function valid(...$params){
		return $this -> getValue() !== null;
	}
}
?>