<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";
require_once __ROOT__ . "/model/node/xml_node.php";

class XMLFlow extends Flow{
	
	public static array $flowPaths = array(
								array(XmlStartTag::class, Xml::class, array(AttributeFlow::class), XmlEndTag::class)
							);

    public function __construct(){
        
    }

    public function constructNode(){
        $flowElements = $this -> flowElements;
        $node = new XMLNode();
        
        foreach ($flowElements as $flowElement){
			$isConstructed = $flowElement -> constructNode();
			$constructedFlowNode = $flowElement -> getConstructedNode();
			
			if($isConstructed === false){
				return false;
			}
			
			if(is_a($flowElement, Xml::class)){
				$node -> setLabel($flowElement -> getValue());
            }
			
            else if(is_a($flowElement, AttributeFlow::class)){
				array_push($node -> getAttributeNodes(), $constructedFlowNode);
            }
        }
		
        $this -> constructedNode = $node;
		
		return $this -> constructedNode -> valid();
    }
	
	

}

?>