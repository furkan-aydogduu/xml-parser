<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/model/node/node.php";

class CDataNode extends Node {
    
	public function valid(...$params){
		return true;
	}
}

?>
