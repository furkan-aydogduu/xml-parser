<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";
require_once __ROOT__ . "/model/node/document_node.php";

class DocumentFlow extends Flow{
 
	public static array $flowPaths = array(
								array(XMLFlow::class, DataFlow::class),
								array(XMLFlow::class, CommentFlow::class, DataFlow::class),
								array(DocumentFlow::class, CommentFlow::class)
							);
	
    public function __construct(){
        
    }
    
    public function constructNode(){
        $node = new DocumentNode();
		
		$documentNodeFromSubFLow = null;
		
		$flowElements = $this -> flowElements;
        
        foreach ($flowElements as $flowElement){
			
			$isConstructed = $flowElement -> constructNode();

			if($isConstructed === false){
				return false;
			}
			
			$constructedFlowNode = $flowElement -> getConstructedNode();
				
			if(is_a($flowElement, DocumentFlow::class)){
				$documentNodeFromSubFLow = $constructedFlowNode;
			}
			else if(is_a($flowElement, XMLFlow::class)){

				foreach ($constructedFlowNode -> getAttributeNodes() as $xmlAttributeNode){
					if(strtolower($xmlAttributeNode -> getLabel()) === 'version'){
						$node -> setVersion($xmlAttributeNode -> getValue());
					}
					else if(strtolower($xmlAttributeNode -> getLabel()) === 'encoding'){
						$node -> setEncoding($xmlAttributeNode -> getValue());
					}
					else if(strtolower($xmlAttributeNode -> getLabel()) === 'standalone'){
						$node -> setStandalone($xmlAttributeNode -> getValue());
					}
				}
				array_push($node -> getSubNodes(), $constructedFlowNode);
			}
		   
			else if(is_a($flowElement, DataFlow::class)){
				array_push($node -> getSubNodes(), $constructedFlowNode);
			}
        }
		
		if($documentNodeFromSubFLow === null){
			$this -> constructedNode = $node;
		}
		else{
			$this -> constructedNode = $documentNodeFromSubFLow;
		}
		
		return $this -> constructedNode -> valid($documentNodeFromSubFLow, $node);
    }
}

?>