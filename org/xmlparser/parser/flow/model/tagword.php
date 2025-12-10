<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/model/flow_element.php";

class TagWord extends FlowElement{
    
	public function constructNode(){
        $node = new TextNode($this -> getValue());
        $this -> constructedNode = $node;
		return true;
    }
}

?>