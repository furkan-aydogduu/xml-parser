<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/model/flow_element.php";

class UnreasonableWord extends FlowElement{
    
	public function constructNode(){
		return false;
    }
}

?>