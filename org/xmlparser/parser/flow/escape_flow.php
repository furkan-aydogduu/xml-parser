<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";
require_once __ROOT__ . "/model/node/escape_node.php";

class EscapeFlow extends Flow{

	public static array $flowPaths = array(
								array(EscapeStartTag::class, TextFlow::class, EscapeEndTag::class)
						 );

    public function __construct(){
        
    }
    
    public function constructNode(){
		
		$node = new EscapeNode();
		
		$flowElements = $this -> flowElements;
		
		foreach ($flowElements as $flowElement){
			
			$isConstructed = $flowElement -> constructNode();
			$constructedFlowNode = $flowElement -> getConstructedNode();
			
			if($isConstructed === false){
				return false;
			}
			
            if(is_a($flowElement, TextFlow::class)){
				$node -> setValue($constructedFlowNode -> getValue() === null ? "" : $constructedFlowNode -> getValue());
            }
        }
		
		$this -> constructedNode = $node;
		
		return $this -> constructedNode -> valid();
    }
}

?>