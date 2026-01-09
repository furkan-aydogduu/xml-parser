<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/flow.php";

class CommentFlow extends Flow{
    
	public static array $flowPaths = array(
							array(CommentStartTag::class, TextFlow::class, CommentEndTag::class)
					 );

    public function __construct(){
       
    }
   
    public function constructNode(){
		return true;
    }
}

?>