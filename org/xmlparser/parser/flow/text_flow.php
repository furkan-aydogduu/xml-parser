<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";
require_once __ROOT__ . "/model/node/text_node.php";

class TextFlow extends Flow{

	public static array $flowPaths = array(
							array(
									FreeWord::class, EscapeFlow::class
								),
							array(
									EscapeFlow::class, FreeWord::class
								),
							array(
									EscapeFlow::class, TextFlow::class
								),
							array(
									TextFlow::class, EscapeFlow::class
							),
							array(
									TextFlow::class, FreeWord::class
							),
							array(
									FreeWord::class, TextFlow::class
							),
							array(
									EscapeFlow::class
							),
							array(
									FreeWord::class
							)
						);

    public function __construct(){
        
    }
    
    public function constructNode(){
		
		$nodeValue = "";
		
		$flowElements = $this -> flowElements;
		
		foreach ($flowElements as $flowElement){
			
			$isConstructed = $flowElement -> constructNode();
			$constructedFlowNode = $flowElement -> getConstructedNode();
			
			if($isConstructed === false){
				return false;
			}
			
            if(is_a($flowElement, FreeWord::class)){
				$nodeValue = $nodeValue . ($flowElement -> getValue() === null ? "" : $flowElement -> getValue());
            }
			else if(is_a($flowElement, EscapeFlow::class)){
				$nodeValue = $nodeValue . $constructedFlowNode -> getValue();
			}
			else if(is_a($flowElement, TextFlow::class)){
				$nodeValue = $nodeValue . $constructedFlowNode -> getValue();
			}
        }
		
        $node = new TextNode($nodeValue);
		
        $this -> constructedNode = $node;
		
		return $this -> constructedNode -> valid();
    }
}

?>