<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/model/node/node.php";

class XMLNode extends Node{
   
    public function valid(...$params){
		
		$isVersionAttributeValidated = true;
		$isEncodingAttributeValidated = true;
		$isStandaloneAttributeValidated = true;
		
		foreach ($this -> getAttributeNodes() as $attributeNode){

			if(strtolower($attributeNode -> getLabel()) === 'version'){
				if($attributeNode -> getValue() === null){
					$isVersionAttributeValidated = false;
				}
			}
			
			else if(strtolower($attributeNode -> getLabel()) === 'encoding'){
				if($attributeNode -> getValue() === null){
					$isEncodingAttributeValidated = false;
				}
			}
			
			else if(strtolower($attributeNode -> getLabel()) === 'standalone'){
				if($attributeNode -> getValue() === null){
					$isStandaloneAttributeValidated = false;
				}
			}
			
			else{
				return false;
			}
        }
		
		return $isVersionAttributeValidated
			&& $isEncodingAttributeValidated
			&& $isStandaloneAttributeValidated
			&& strtoLower($this -> getLabel()) === XML;
	}
}
?>