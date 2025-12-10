<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/model/node/node.php";

class XMLNode extends Node{
   
    public function valid(...$params){
		
		$isVersionAttributeValidated = false;
		$isEncodingAttributeValidated = false;
		$isStandaloneAttributeValidated = false;
		
		foreach ($this -> getAttributeNodes() as $attributeNode){
			if(strtolower($attributeNode -> getLabel()) === 'version'){
				if($attributeNode -> getValue() !== null){
					$isVersionAttributeValidated = true;
				}
			}
			
			else if(strtolower($attributeNode -> getLabel()) === 'encoding'){
				if($attributeNode -> getValue() !== null){
					$isEncodingAttributeValidated = true;
				}
			}
			
			else if(strtolower($attributeNode -> getLabel()) === 'standalone'){
				if($attributeNode -> getValue() !== null){
					$isStandaloneAttributeValidated = true;
				}
			}
        }
		
		return $isVersionAttributeValidated
			&& $isEncodingAttributeValidated
			&& $isStandaloneAttributeValidated
			&& count($this -> getAttributeNodes()) === 3
			&& strtoLower($this -> getLabel()) === XML;
	}
}
?>