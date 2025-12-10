<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/model/node/node.php";

class AttributeNode extends Node {
    
	public function valid(...$params){
		return $this -> getLabel() !== null && strlen(trim($this -> getLabel())) > 0;
	}
}

?>