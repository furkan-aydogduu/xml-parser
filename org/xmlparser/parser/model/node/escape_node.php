<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/model/node/node.php";
require_once __ROOT__ . "/flow/controller/flow_controller.php";

class EscapeNode extends Node{
    
	public function valid(...$params){
		
		$nodeValue = $this -> getValue();
		
		if($nodeValue !== null){
			$nodeValue = ESCAPE_START_TAG . strtolower($nodeValue) . ESCAPE_END_TAG;
		}
		 
		return 	$this -> getValue() !== null 
			 && strlen($this -> getValue()) > 0
			 && (
					strpos($nodeValue, ESCAPED_AMPERSAND) !== false
				 || strpos($nodeValue, ESCAPED_LEFT_ANGLE_BRACKET) !== false 
				 || strpos($nodeValue, ESCAPED_RIGHT_ANGLE_BRACKET) !== false 
				 || strpos($nodeValue, ESCAPED_SINGLE_QUOTE) !== false 
				 || strpos($nodeValue, ESCAPED_DOUBLE_QUOTE) !== false 
			 
				);
	}
}
?>