<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";
require_once __ROOT__ . "/model/node/cdata_node.php";

class CDataFlow extends Flow{
    
	public static array $flowPaths = array(
							array(CDataStartTag::class, array(TextFlow::class), CDataEndTag::class)
					 );

    public function __construct(){
       
    }
   
    public function constructNode(){
		$flowElements = $this -> flowElements;
        $node = new CDataNode();
        
        foreach ($flowElements as $flowElement){
			$isConstructed = $flowElement -> constructNode();
			$constructedFlowNode = $flowElement -> getConstructedNode();
			
			if($isConstructed === false){
				return false;
			}
			
            if(is_a($flowElement, TextFlow::class)){

				if($node -> getValue() === null){
					$node -> setValue($constructedFlowNode -> getValue());
				}
				else{
					$node -> setValue($node -> getValue() . $constructedFlowNode -> getValue());
				}

                array_push($node -> getSubNodes(), $constructedFlowNode);
            }
        }
 
        $this -> constructedNode = $node;
		
		return $this -> constructedNode -> valid();
    }
}

?>