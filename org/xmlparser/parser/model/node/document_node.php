<?php
namespace org\xmlparser\parser;

require_once __ROOT__ . "/model/node/node.php";

class DocumentNode extends Node{
    private $version;
    private $encoding;
    private $standalone;
    
    public function getVersion(){
        return $this -> version;
    }
    
    public function getEncoding(){
        return $this -> encoding;
    }
    
    public function getStandalone(){
        return $this -> standalone;
    }
    
    public function setVersion($version){
        return $this -> version = $version;
    }
    
    public function setEncoding($encoding){
        return $this -> encoding = $encoding;
    }
    
    public function setStandalone($standalone){
        return $this -> standalone = $standalone;
    }
	
	public function valid(...$params){
		$documentNodeFromSubFLow = $params[0];
		$newCreatedDocumentNode = $params[1];
		
		if($this === $documentNodeFromSubFLow && count($newCreatedDocumentNode -> getSubNodes()) > 0){
			return false;
		}
		
		return true;
	}
}
?>