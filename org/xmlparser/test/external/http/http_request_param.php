<?php
namespace org\xmlparser\test\external\http;

class HTTPRequestParam {
    
	private $paramName;
	private $value;
	
	public function __construct($paramName, $value){
        $this -> paramName = $paramName;
		$this -> value = $value;
    }
	
	public function getParamName(){
		return $this -> paramName;
	}
	
	public function getParamValue(){
		return $this -> value;
	}
	
	public function setParamValue($value){
		$this -> value = $value;
	}
}

?>