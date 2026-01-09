<?php 
namespace org\xmlparser\parser;

define('__ROOT__', dirname(__FILE__));

define("LIBRARY_NAME", "xmlparser");
define("LIBRARY_VERSION", "1.3");

require_once __ROOT__ . "/flow/controller/flow_controller.php";
require_once __ROOT__ . "/flow/controller/flow_pipe_controller.php";

class XMLParser {
    
    private $xmlInputAsString;
    private $flowController;
	private $flowPipeController;
	
    public function __construct($xmlInputAsString = ""){
        $this -> xmlInputAsString = $xmlInputAsString;
		$this -> flowController = new FlowController();
		$this -> flowPipeController = new FlowPipeController();
    }
    
    public function parseXMLAndConvertToDocument(){
		$xmlDocument = null;
		
        $xmlInputAsString = $this -> xmlInputAsString;
		
		if(gettype($xmlInputAsString) === "string"){
			$xmlInputAsString = str_replace("\r", "", $xmlInputAsString);
			$xmlInputAsString = str_replace("\n", "", $xmlInputAsString);
			$xmlInputAsString = str_replace("\r\n", "", $xmlInputAsString);
			$xmlInputAsString = str_replace("\n\r", "", $xmlInputAsString);
			
			$flowStream = str_split($xmlInputAsString);
			
			$isSenseSuccess = $this -> flowController -> senseFlow($flowStream);

			//$this -> printFlowPipe($this -> flowController -> getFlowPipe());
			
			//$this -> flowController -> printSemiStatefulDataFlow();
			
			if($isSenseSuccess){
				$this -> flowPipeController -> setFlowPipe($this -> flowController -> getFlowPipe());
				$xmlDocument = $this -> flowPipeController -> generateXMLDocumentFromFlowPipe();
			}
		}
		else{
			echo "incorrect input format: " . gettype($xmlInputAsString) . " ! -> should be string\n";
		}
        
		//$this -> printNode($xmlDocument);
		
		$this -> flowController -> finalize();
        return $xmlDocument;
    }
	
	public function printFlowPipe($flowPipe){
		
		$pipeElementCount = count($flowPipe);
		
		echo "flow pipe element count: " . $pipeElementCount . "\n";
		
		for($i = 0; $i < $pipeElementCount; $i++){
			echo "flow pipe: " . get_class($flowPipe[$i]) . " " . "`" . $flowPipe[$i] -> getValue() . "`" . "\n";
		}
		
		if($pipeElementCount == 0){
			echo "flow pipe: pipe is empty!\n";
		}
		
		echo "-----------------\n";
	}
	
	public function printNode($node){
		$indentation = "    ";
		
		$isPrintable = $this -> printNodeInternal($node, "", $indentation);

		if(!$isPrintable){
			echo "node: unstable node structure!\n";
		}
		
		echo "-----------------\n";
		
	}
	
	public function printNodeInternal($node, $initialIndentation, $indentation){
		$isPrintable = true;
		
		if($node === null){
			return false;
		}
		
		$childCount = count($node -> getSubNodes());
		
		$nodeVal = null;
		
		if(is_a($node, DocumentNode::class)){
			$nodeVal = "-> [ " . "version: '" . $node -> getVersion() . "' encoding: '" . $node -> getEncoding() . "' standalone: '" . $node -> getStandalone() ."' ]";
		}
		else if(is_a($node, TextNode::class) || is_a($node, CDataNode::class)){
			$nodeVal = "-> [" . substr($node -> getValue(), 0, 20) . (strlen($node -> getValue()) > 20 ? "..." : "") . "]";
		}
		else{
			$nodeVal = "-> [" . substr($node -> getLabel(), 0, 20) . (strlen($node -> getLabel()) > 20 ? "..." : "") . "]";
			
			if(count($node -> getAttributeNodes()) > 0){
				$nodeVal = $nodeVal . " ["; 
				
				foreach($node -> getAttributeNodes() as $attributeNode){
					$nodeVal = $nodeVal . " " . substr($attributeNode -> getLabel(), 0, 20) . (strlen($attributeNode -> getLabel()) > 20 ? "..." : "") . ": '" . substr($attributeNode -> getValue(), 0, 20) . (strlen($attributeNode -> getValue()) > 20 ? "..." : "") . "'";
				}
				
				$nodeVal = $nodeVal . " ]"; 
			}
		}
		
		echo "node:" . $initialIndentation . get_class($node) . ", childCount: " . $childCount . " ". $nodeVal  ."\n";
		
		$initialIndentation = $initialIndentation . $indentation;
		
		for($i = 0; $i < $childCount; $i++){
			$isPrintable = $isPrintable && $this -> printNodeInternal($node -> getSubNodes()[$i], $initialIndentation, $indentation);
		}
		
		return $isPrintable;
	}
	
	public function getNodeCount($node){
		$totalCount = 1;
		
		if($node === null){
			return false;
		}
		
		$childCount = count($node -> getSubNodes());
		
		for($i = 0; $i < $childCount; $i++){
			$totalCount = $totalCount + $this -> getNodeCount($node -> getSubNodes()[$i]);
		}
		
		return $totalCount;
	}
	
	public function setXMLInput($xmlInputAsString){
		$this -> xmlInputAsString = $xmlInputAsString;
	}
}



?>