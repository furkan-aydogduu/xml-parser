<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/model/node/node.php";

class DataNode extends Node {
    
	public function valid(...$params){
		
		if($this -> getLabel() === null){
			return false;
		}
		else if(strlen(trim($this -> getLabel())) < 1){
			return false;
		}
		
		//check for duplicate attribute definition
		foreach($this -> getAttributeNodes() as $attributeNode1){
			foreach($this -> getAttributeNodes() as $attributeNode2){
				if($attributeNode1 !== $attributeNode2 && $attributeNode1 -> getLabel() === $attributeNode2 -> getLabel()){
					//echo "`" . $this -> getLabel() . "`" . $attributeNode1 -> getLabel() . "`\n";
					return false;
				}
			}
		}
		
		return true;
	}
}

?>
