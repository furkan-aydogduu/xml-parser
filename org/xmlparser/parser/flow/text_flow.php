<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";
require_once __ROOT__ . "/model/node/text_node.php";

class TextFlow extends Flow{

	public static array $flowPaths = array(
							array(FreeWord::class)
						);

    public function __construct(){
        
    }
    
    public function constructNode(){
        $flowElement = $this -> flowElements[0];
		
        $node = new TextNode($flowElement -> getValue() === null ? "" : $flowElement -> getValue());
		
        $this -> constructedNode = $node;
		
		return $this -> constructedNode -> valid();
    }
}

?>