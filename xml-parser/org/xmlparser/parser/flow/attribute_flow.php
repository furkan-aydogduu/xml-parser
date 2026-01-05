<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";
require_once __ROOT__ . "/model/node/attribute_node.php";

class AttributeFlow extends Flow{

	public static array $flowPaths = array(
								array(AttributeTagWord::class, Equals::class, Quote::class, array(TextFlow::class), Quote::class)
						 );

    public function __construct(){
        
    }
    
    public function constructNode(){
        $flowElements = $this -> flowElements;
        $node = new AttributeNode();
        
        foreach ($flowElements as $flowElement){
			$isConstructed = $flowElement -> constructNode();
			$constructedFlowNode = $flowElement -> getConstructedNode();
			
			if($isConstructed === false){
				return false;
			}
				
            if(is_a($flowElement, AttributeTagWord::class)){
				$node -> setLabel($constructedFlowNode -> getValue());
            }
            else if (is_a($flowElement, TextFlow::class)){
				if($node -> getValue() !== null){
					$node -> setValue($node -> getValue() . $constructedFlowNode -> getValue());
				}
				else{
					$node -> setValue($constructedFlowNode -> getValue());
				}
            }
        }

        $this -> constructedNode = $node;
		
		return $this -> constructedNode -> valid();
    }
}

?>