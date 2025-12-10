<?php 
namespace org\xmlparser\parser;

require_once __ROOT__ . "/flow/document_flow.php";
require_once __ROOT__ . "/flow/attribute_flow.php";
require_once __ROOT__ . "/flow/data_flow.php";
require_once __ROOT__ . "/flow/text_flow.php";
require_once __ROOT__ . "/flow/xml_flow.php";
require_once __ROOT__ . "/flow/comment_flow.php";
require_once __ROOT__ . "/flow/cdata_flow.php";

class FlowPipeController {
    
    private $flowPipe;
	
    private static array $legacyStructuredFlows = array(
			TextFlow::class,
			AttributeFlow::class,
			DataFlow::class,
			XmlFlow::class,
			DocumentFlow::class,
			CDataFlow::class, 
			CommentFlow::class
	);
    
    public function __construct($flowPipe){
        $this -> flowPipe = $flowPipe;
    }
    
    public function generateXMLDocumentFromFlowPipe(){
        
        $this -> convertFlowPipeToStructuredDocumentFlow();
      
        //There must be only one xml document flow left in the flow pipe as it represents an xml structure
        if(count($this -> flowPipe) !== 1 || !is_a($this -> flowPipe[0], DocumentFlow::class)){
			$this -> finalize();
			return null;
        }
        
        $documentFlow = $this -> flowPipe[0];
		
        $isConstructed = $documentFlow -> constructNode();
		
		if($isConstructed === false){
			$this -> finalize();
			return null;
		}
		
		$this -> finalize();
        return $documentFlow -> getConstructedNode();
    }
    
    private function convertFlowPipeToStructuredDocumentFlow(){
        $pipeSize = count($this -> flowPipe);
		
        $possibleFlows = FlowPipeController::$legacyStructuredFlows;
		
        for($curDataFlowIndex = 0; $curDataFlowIndex < $pipeSize; $curDataFlowIndex++){
            //$pipeElement = $this -> flowPipe[$curDataFlowIndex];
            
            //$possibleFlows = $this -> getPossiblyCompatibleLegacyFlowsWithPipeElement($pipeElement);

			foreach ($possibleFlows as $possibleFlow){
				$flowConversionApplied = false;
				
				$relatedFlowPaths = $possibleFlow::getFlowPaths();
				
				$relatedFlowPathSize = count($relatedFlowPaths);
				
				for($curFlowPathIndexOfCurFlow = 0; $curFlowPathIndexOfCurFlow < $relatedFlowPathSize; $curFlowPathIndexOfCurFlow++){
					$relatedFlowPath = $relatedFlowPaths[$curFlowPathIndexOfCurFlow];
					
					$offsetDataFlowIndex = $curDataFlowIndex;
				
					$flowPathIndex = 0;
					$pathSize = count($relatedFlowPath);
					//echo $pathSize . "\n" ;
					//echo $pipeSize . "\n";
					//echo  $offsetDataFlowIndex ."\n";
					
					//echo (is_a($this -> flowPipe[$pipeSize - 1], TextFlow::class) === false ? "false" : "true") . "\n";
					$isFlowSuitableForPath = true;
					
					for(;$flowPathIndex < $pathSize && $offsetDataFlowIndex < $pipeSize;){
						$offsetPipeElement = $this -> flowPipe[$offsetDataFlowIndex];
						$relatedFlowElement = $relatedFlowPath[$flowPathIndex];

						if(is_array($relatedFlowElement)){
							
							if(!$this -> flowContainsPipeElement($relatedFlowElement, $offsetPipeElement)){
								$offsetDataFlowIndex--;
							}
							else {
								$flowPathIndex--;
							}
						}
						else if(!is_a($offsetPipeElement, $relatedFlowElement)){
							$isFlowSuitableForPath = false;
							break;
						}
						
						$flowPathIndex++;
						$offsetDataFlowIndex++;
					}
					
					//meet flow path requirements, so convert it to corresponding flow in the flow pipe
					if($flowPathIndex === $pathSize && $isFlowSuitableForPath){
						
						$sliceLength = $offsetDataFlowIndex - $curDataFlowIndex;

						$flowInstance = new $possibleFlow;
					
						for($i = $curDataFlowIndex; $i < $offsetDataFlowIndex; $i++){
							$offsetPipeElement = $this -> flowPipe[$i];
							//echo get_class($offsetPipeElement) . "\n";
							$flowInstance -> insertFlowElement($offsetPipeElement);
						}
						
						array_splice($this -> flowPipe, $curDataFlowIndex, $sliceLength, array($flowInstance));

						//echo " offset: " . $offsetDataFlowIndex . "\n";
						//echo " curdataoffset: " . $curDataFlowIndex . "\n";
						//echo " flowpathindex: " . $flowPathIndex ."\n";
						
						//echo "zz " . $_flowInstance -> getFlowElements()[count($_flowInstance -> getFlowElements()) - 1] -> getValue() . "\n";
						//echo "class: " . get_class($_flowInstance). "\n";
						//echo "count: ". count($_flowInstance -> getFlowElements()). "\n";
						//echo "pipesize: " . count($this -> flowPipe) . "\n";

						$flowConversionApplied = true;
						$pipeSize = count($this -> flowPipe); //recalculate pipe size because of the reduce operation in the flow
						$curDataFlowIndex = -1;
						break;
					}
				}
				
				if($flowConversionApplied){
					//echo "--------------\n";
					break;
				}
			}
        }
    }
   
    public function getFlowPipe(){
        return $this -> flowPipe;
    }
    
    private function flowContainsPipeElement($flowElement, $pipeElement){
        foreach ($flowElement as $subFlow){
            if(is_a($pipeElement, $subFlow)){
                return true;
            }
        }
        return false;
    }
	
	private function finalize(){
		unset($this -> flowPipe);
	}
    
}

?>