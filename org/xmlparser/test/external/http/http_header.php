<?php
namespace org\xmlparser\test\external\http;

class HTTPHeader {
    
	private $headerName;
	private $value;
	
	public function __construct($headerName, $value){
        $this -> headerName = $headerName;
		$this -> value = $value;
    }
	
	public function getHeaderName(){
		return $this -> headerName;
	}
	
	public function getHeaderValue(){
		return $this -> value;
	}
	
	public function setHeaderValue($value){
		return $this -> value;
	}
}

?>