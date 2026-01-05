<?php
namespace org\xmlparser\parser;

abstract class Flow{
    
    protected static array $flowPaths;
    protected array $flowElements = array();
    protected $constructedNode;
    
    public static function getFlowPaths(){
		$calledClass = get_called_class();
		
        return $calledClass::$flowPaths;
    }
    
    public function getConstructedNode(){
        return $this -> constructedNode;
    }
    
    public function getFlowElements(){
        return $this -> flowElements;
    }
    
    public function insertFlowElement($flowElement){
        array_push($this -> flowElements, $flowElement);
    }
    
	//has no use for now
    /*public function complyWithElement($flowElement){
        return is_a($flowElement, $this -> flowPath[0]);
    }*/
    
    public abstract function constructNode();
    
}

?>