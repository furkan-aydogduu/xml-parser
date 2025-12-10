<?php 
namespace org\xmlparser\parser;

abstract class Node {
    protected $label;
    protected $subNodes;
    protected $value;
    protected $attributeNodes;
    protected $additionalData;
    
    public function __construct(){
        $this -> attributeNodes = array();
        $this -> subNodes = array();
    }
    
    public function &getLabel(){
        return $this -> label;
    }
    
    public function &getSubNodes(){
        return $this -> subNodes;
    }
    
    public function &getValue(){
        return $this -> value;
    }
    
    public function &getAttributeNodes(){
        return $this -> attributeNodes;
    }
    
    public function &getAdditionalData(){
        return $this -> additionalData;
    }
    
    public function setLabel($label){
        return $this -> label = $label;
    }
    
    public function setSubNodes($subNodes){
        return $this -> subNodes = $subNodes;
    }
    
    public function setValue($value){
        return $this -> value = $value;
    }
    
    public function setAttributeNodes($attributes){
        return $this -> attributeNodes = $attributes;
    }
    
    public function setAdditionalData($additionalData){
        return $this -> additionalData = $additionalData;
    }
	
	public function valid(...$params){}
}
?>