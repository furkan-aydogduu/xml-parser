<?php 
namespace org\xmlparser\parser;

/**
 * e.g. : array(DataFlow::class, TextFlow::class) -> means that any number of data flow and/or 
 *        text flow can exist in any order
 * e.g. : array(AttributeFlow::class) -> means also that any number of attribute flow can exist
 * */

require_once __ROOT__ . "/flow/flow.php";
require_once __ROOT__ . "/model/node/data_node.php";

class DataFlow extends Flow{
    
	public static array $flowPaths = array(
								array(
									StartStartTag::class, TagWord::class, array(AttributeFlow::class), StartEndTag::class, 
										array(DataFlow::class, TextFlow::class, CDataFlow::class, CommentFlow::class),
									EndStartTag::class, TagWord::class, EndEndTag::class
								)
						 );

    public function __construct(){
        
    }
   
    public function constructNode(){
        $flowElements = $this -> flowElements;
        $node = new DataNode();
        
        foreach ($flowElements as $flowElement){
			$isConstructed = $flowElement -> constructNode();
			$constructedFlowNode = $flowElement -> getConstructedNode();
			
			if($isConstructed === false){
				return false;
			}
			
            if(is_a($flowElement, AttributeFlow::class)){
                array_push($node -> getAttributeNodes(), $constructedFlowNode);
            }
			
            else if(is_a($flowElement, DataFlow::class)){
                array_push($node -> getSubNodes(), $constructedFlowNode);
            }
			
			else if(is_a($flowElement, CDataFlow::class)){
                array_push($node -> getSubNodes(), $constructedFlowNode);
            }
			
            else if(is_a($flowElement, TextFlow::class)){

				if($node -> getValue() === null){
					$node -> setValue($constructedFlowNode -> getValue());
				}
				else{
					$node -> setValue($node -> getValue() . $constructedFlowNode -> getValue());
				}

                array_push($node -> getSubNodes(), $constructedFlowNode);
            }
            else if(is_a($flowElement, TagWord::class)){
                if($node -> getLabel() !== null){
                    if(strtolower($constructedFlowNode -> getValue()) !== strtolower($node -> getLabel())){
						$node -> setLabel(null);
                    }
                }
                else{
                    $node -> setLabel($constructedFlowNode -> getValue());
                }
            }
        }
 
        $this -> constructedNode = $node;
		
		return $this -> constructedNode -> valid();
    }
}

?>