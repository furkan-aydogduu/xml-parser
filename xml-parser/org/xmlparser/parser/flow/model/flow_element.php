<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";

abstract class FlowElement extends Flow{
    private $value;
    
    public function __construct($value){
        $this -> value = $value;    
    }
    
    public function getValue(){
        return $this -> value;
    }
}

?>