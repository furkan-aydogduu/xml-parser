<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/model/flow_element.php";

class CommentEndTag extends FlowElement{
    
    public function constructNode(){
		return true;
    }
}

?>