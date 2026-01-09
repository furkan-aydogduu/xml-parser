<?php 
namespace org\xmlparser\parser;

define("CDATA_START_TAG",				"<![cdata[");
define("CDATA_END_TAG",					"]]>");
define("COMMENT_START_TAG",				"<!--");
define("COMMENT_END_TAG",				"-->");
define("DOUBLE_HYPEN",					"--");
define("START_START_TAG",				"<");
define("START_END_TAG",					">");
define("END_START_TAG",					"</");
define("END_END_TAG",					">");
define("EMPTY_END_TAG",					"/>");
define("XML_START_TAG",					"<?");
define("XML_END_TAG",					"?>");
define("XML",							"xml");
define("EMPTY_STRING",					"");
define("EQUALS",						"=");
define("SINGLE_QUOTE",					"'");
define("DOUBLE_QUOTE",					"\"");
define("ESCAPE_START_TAG",				"&");
define("ESCAPE_END_TAG",				";");

define("ESCAPED_AMPERSAND",				"&amp;");
define("ESCAPED_LEFT_ANGLE_BRACKET",	"&lt;");
define("ESCAPED_RIGHT_ANGLE_BRACKET",	"&gt;");
define("ESCAPED_SINGLE_QUOTE",			"&apos;");
define("ESCAPED_DOUBLE_QUOTE",			"&quote;");


define("VALID_START_CHAR_CODE_RANGES", array(
	(object)['start' => 58, 'end' => 58],		// :
	(object)['start' => 65,'end' => 90], 		//A-Z
	(object)['start' => 95, 'end' => 95], 		//_
	(object)['start' => 97, 'end' => 122],		//a-z
	(object)['start' => 192, 'end' => 214], (object)['start' => 216, 'end' => 246], (object)['start' => 248, 'end' => 767], (object)['start' => 880, 'end' => 893],
	(object)['start' => 895, 'end' => 8191], (object)['start' => 8204, 'end' => 8205], (object)['start' => 8304, 'end' => 8591], (object)['start' => 11264, 'end' => 12271],
	(object)['start' => 12289, 'end' => 55295], (object)['start' => 63744, 'end' => 64975],	(object)['start' => 65008, 'end' => 65533],  (object)['start' => 65536, 'end' => 983039]
));

define("VALID_NAME_CHAR_CODE_RANGES", array(
	...VALID_START_CHAR_CODE_RANGES,
	(object)['start' => 45, 'end' => 45],		//-
	(object)['start' => 46,'end' => 46], 		//.
	(object)['start' => 48, 'end' => 57], 		//0-9
	(object)['start' => 183, 'end' => 183], (object)['start' => 768, 'end' => 879], (object)['start' => 8255, 'end' => 8256]
));

require_once __ROOT__ . "/flow/model/attributetagword.php";
require_once __ROOT__ . "/flow/model/end_end_tag.php";
require_once __ROOT__ . "/flow/model/empty_end_tag.php";
require_once __ROOT__ . "/flow/model/end_start_tag.php";
require_once __ROOT__ . "/flow/model/escape_start_tag.php";
require_once __ROOT__ . "/flow/model/escape_end_tag.php";
require_once __ROOT__ . "/flow/model/equals.php";
require_once __ROOT__ . "/flow/model/freeword.php";
require_once __ROOT__ . "/flow/model/quote.php";
require_once __ROOT__ . "/flow/model/start_end_tag.php";
require_once __ROOT__ . "/flow/model/start_start_tag.php";
require_once __ROOT__ . "/flow/model/comment_start_tag.php";
require_once __ROOT__ . "/flow/model/comment_end_tag.php";
require_once __ROOT__ . "/flow/model/cdata_start_tag.php";
require_once __ROOT__ . "/flow/model/cdata_end_tag.php";
require_once __ROOT__ . "/flow/model/tagword.php";
require_once __ROOT__ . "/flow/model/xml_end_tag.php";
require_once __ROOT__ . "/flow/model/xml_start_tag.php";
require_once __ROOT__ . "/flow/model/xml.php";
require_once __ROOT__ . "/flow/model/unreasonableword.php";

class FlowController {

	private $hasCDataStartTag;
	private $hasEscapeStartTag;
	private $hasCommentStartTag;
    private $hasStartStartTag;
    private $hasEndStartTag;
    private $hasEndEndTag;
    private $hasStartEndTag;
    private $hasXMLStartTag;
    private $hasXMLEndTag;
    private $hasXml;
    private $hasTagWord;
    private $hasAttributeTagWord;
    private $hasEquals;
    private $hasQuote;
	private $hasWhitespace;
	
    private $semiStatefulDataFlow;
    private $semiStatefulDataFlowFuture;
    private $currentQuote;
	
	private array $flowPipe;
	
	public function __construct(){
		$this -> hasCDataStartTag = false;
		$this -> hasEscapeStartTag = false;
		$this -> hasCommentStartTag = false;
		$this -> hasStartStartTag = false;
		$this -> hasEndStartTag = false;
		$this -> hasEndEndTag = false;
		$this -> hasStartEndTag = false;
		$this -> hasXMLStartTag = false;
		$this -> hasXMLEndTag = false;
		$this -> hasXml = false;
		$this -> hasTagWord = false;
		$this -> hasAttributeTagWord = false;
		$this -> hasEquals = false;
		$this -> hasQuote = false;
		$this -> hasWhitespace = false;
		
		$this -> semiStatefulDataFlow = EMPTY_STRING;
		$this -> semiStatefulDataFlowFuture = EMPTY_STRING;
		$this -> currentQuote = EMPTY_STRING;

        $this -> flowPipe = array();
    }
	
	private function is_white_space($flow){
        return strlen($flow) > 0 && strlen(trim($flow)) === 0;
    }
	
    private function is_start_start_tag($flow){
        return $flow === START_START_TAG;
    }
    
    private function is_start_end_tag($flow, $hasStartStartTag, $hasTagWord, $hasQuote){
        return !$hasQuote && $hasStartStartTag && $hasTagWord && $flow === START_END_TAG;
    }
    
    private function is_end_start_tag($flow){
        return $flow === END_START_TAG;
    }
    
    private function is_end_end_tag($flow, $hasEndStartTag, $hasTagWord){
        return $hasEndStartTag && $hasTagWord && $flow === END_END_TAG;
    }
	
	private function is_empty_end_tag($flow, $hasStartStartTag, $hasTagWord, $hasQuote){
        return !$hasQuote && $hasStartStartTag && $hasTagWord && $flow === EMPTY_END_TAG;
    }
    
    private function is_xml_start_tag($flow){
        return $this -> isPipeEmpty() && $flow === XML_START_TAG;
    }
    
    private function is_xml_end_tag($flow, $hasXml, $hasQuote){
        return !$hasQuote && $hasXml && $flow === XML_END_TAG;
    }
    
    private function is_xml($flow, $hasXMLStartTag, $hasXml){
        return !$hasXml && $hasXMLStartTag && strpos(XML, strtolower($flow)) !== false;
    }
	
	private function is_comment_start_tag($flow, $hasCommentStartTag){
        return !$hasCommentStartTag && $flow === COMMENT_START_TAG;
    }
	
	private function is_comment_end_tag($flow, $hasCommentStartTag){
		return $hasCommentStartTag && substr($flow, -strlen(COMMENT_END_TAG)) === COMMENT_END_TAG;
    }
	
	private function appearsToBeCommentStartTag($flow, $hasCommentStartTag){
		
		if(!$hasCommentStartTag){
			
			$commentStartTagLength = strlen(COMMENT_START_TAG);
			$flowDataLength = strlen($flow);

			$i = 0;
			$k = max($flowDataLength - strlen(COMMENT_START_TAG), 0);
			for( ; $i < $commentStartTagLength && $k < $flowDataLength; $i++, $k++){
				if($flow[$k] !== COMMENT_START_TAG[$i]){
					break;
				}
			}
			
			if($i === $commentStartTagLength || $k === $flowDataLength){
				return true;
			}
			
			/*$commentStartTagLength = strlen(COMMENT_START_TAG);

			$i = 1;
			for( ; $i <= $commentStartTagLength; $i++){
				$subCommentStartTag = substr(COMMENT_START_TAG, 0, $i);
				
				if(substr($flow, -$i) === $subCommentStartTag){
					return true;
				}
			}*/
		}

		return false;
	}
	
	private function appearsToBeCommentEndTag($flow, $hasCommentStartTag){
		
		if($hasCommentStartTag){
			
			$commentEndTagLength = strlen(COMMENT_END_TAG);

			$i = $commentEndTagLength;
			
			for( ; $i > 0; $i--){
				
				$subCommentEndTag = substr(COMMENT_END_TAG, 0, $i);
				
				if(substr($flow, -$i) === $subCommentEndTag){
					
					if(strpos($flow, COMMENT_END_TAG) === false || $i === $commentEndTagLength){
						return true;
					}
				}
			}
		}
		
		return false;
    }
	
	private function is_cdata_start_tag($flow, $hasCDataStartTag){
        return !$hasCDataStartTag && strtolower($flow) === CDATA_START_TAG;
    }
	
	private function is_cdata_end_tag($flow, $hasCDataStartTag){
		return $hasCDataStartTag && substr($flow, -strlen(CDATA_END_TAG)) === CDATA_END_TAG;
    }
	
	private function appearsToBeCDataStartTag($flow, $hasCDataStartTag){
		
		if(!$hasCDataStartTag){
			
			$cDataStartTagLength = strlen(CDATA_START_TAG);
			$flowDataLength = strlen($flow);
			
			$i = 0;
			$k = max($flowDataLength - strlen(CDATA_START_TAG), 0);
			
			for( ; $i < $cDataStartTagLength && $k < $flowDataLength; $i++, $k++){
				if(strtolower($flow[$k]) !== CDATA_START_TAG[$i]){
					break;
				}
			}
			
			if($i === $cDataStartTagLength || $k === $flowDataLength){
				return true;
			}
			
			/*$cDataStartTagLength = strlen(CDATA_START_TAG);
			
			$i = 1;
			for( ; $i <= $cDataStartTagLength; $i++){
				$subCDataStartTag = substr(CDATA_START_TAG, 0, $i);
				
				if(substr(strtolower($flow), -$i) === $subCDataStartTag){
					return true;
				}
			}*/
		}
		
		return false;
	}
	
	private function appearsToBeCDataEndTag($flow, $hasCDataStartTag){
		
		if($hasCDataStartTag){
			
			$cDataEndTagLength = strlen(CDATA_END_TAG);
			
			$i = $cDataEndTagLength;
			
			for( ; $i > 0; $i--){
				
				$subCDataEndTag = substr(CDATA_END_TAG, 0, $i);
				
				if(substr($flow, -$i) === $subCDataEndTag){
					
					if(strpos($flow, CDATA_END_TAG) === false || $i === $cDataEndTagLength){
						return true;
					}
				}
			}
		}
		
		return false; 
    }
	
    private function is_equals($flow, $hasStartStartTag, $hasXmlStartTag, $hasTagWord, $hasAttributeTagWord, $hasXml, $hasQuote){
        return ($hasStartStartTag || $hasXmlStartTag) && ($hasTagWord || $hasXml) && $hasAttributeTagWord && !$hasQuote && $flow === EQUALS;
    }
    
    private function is_quote($flow, $hasEquals, $hasStartEndTag, $hasXmlEndTag){
        return $hasEquals && !$hasStartEndTag && !$hasXmlEndTag
		&&	(
				(!$this -> hasQuote && ($flow === DOUBLE_QUOTE || $flow === SINGLE_QUOTE))
					|| 
				($this -> hasQuote && ($flow === $this -> currentQuote))
			);
    }
	
	private function is_escape_start_tag($flow, $hasEscapeStartTag){
        return !$hasEscapeStartTag && $flow === ESCAPE_START_TAG;
    }
	
	private function is_escape_end_tag($flow, $hasEscapeStartTag){
        return $hasEscapeStartTag && substr($flow, -(strlen(ESCAPE_END_TAG))) === ESCAPE_END_TAG;
    }
	
	private function is_escapeword($flow, $hasEscapeStartTag){
        return $hasEscapeStartTag && strpos($flow, ESCAPE_END_TAG) === false && strpos($flow, ESCAPE_START_TAG) === false;
    }
	
    private function is_tagword($flow, $hasStartStartTag, $hasEndStartTag, $hasTagWord){
        return !$hasTagWord && ($hasStartStartTag || $hasEndStartTag)
        && $this -> isFlowCompliesWithTagwordNamingStandard($flow);
    }
    
    private function is_attributetagword($flow, $hasStartStartTag, $hasXMLStartTag, $hasTagWord,
        $hasStartEndTag, $hasXMLEndTag, $hasXml, $hasWhitespace, $hasQuote){
            return 
			   ($hasStartStartTag || $hasXMLStartTag)
            && ($hasTagWord || $hasXml)
			&& $hasWhitespace
            && !$hasStartEndTag && !$hasXMLEndTag && !$hasQuote
            && $this -> isFlowCompliesWithTagwordNamingStandard($flow);
    }
    
    /////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////
    //////////////// Data Flow Sensing //////////////////////////////
    /////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////
   
    public function senseFlow($flowStream){
		$i = 0;
		$streamSize = count($flowStream);
		
		$sensing = true;
		
		while($sensing){
			$flow = $flowStream[$i];

			if($i < $streamSize - 1){
				$nextFlow = $flowStream[$i + 1];
			}
			else{
				$nextFlow = null;
			}
			
			$sensing = $this -> internalSenseFlow($flow, $nextFlow);
			
			if(!$sensing){
				
				$this -> internalFinalize();
				return false;
			}
			else if($nextFlow === null){
				$sensing = false;
			}
			else{
				$i++;
			}
		}
		
		$this -> internalFinalize();
		return true;
	}
	
    private function internalSenseFlow($flow, $nextFlow){
		
        $this -> semiStatefulDataFlow .= $flow;

		if($nextFlow !== null){
			$this -> semiStatefulDataFlowFuture = $this -> semiStatefulDataFlow .$nextFlow;
			
			if($this -> futureSensePossible($this -> semiStatefulDataFlowFuture)){
				return true;
			}
		}
		else{
			$this -> semiStatefulDataFlowFuture = $this -> semiStatefulDataFlow;
		}
		
		if($this -> is_white_space($this -> semiStatefulDataFlow)){
			$this -> hasWhitespace = true;
			
			//echo "ws '".$this -> semiStatefulDataFlow ."'\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
		
        else if($this -> is_xml_start_tag($this -> semiStatefulDataFlow)){
            $flowElement = new XmlStartTag($this -> semiStatefulDataFlow);
            $this -> hasXMLStartTag = true;
            array_push($this -> flowPipe, $flowElement);
			//echo "xml-start ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
 
            return true;
        }
        
        else if($this -> is_xml_end_tag($this -> semiStatefulDataFlow, $this -> hasXml, $this -> hasQuote)){
            $flowElement = new XmlEndTag($this -> semiStatefulDataFlow);
            $this -> hasXml = false;
            $this -> hasXMLStartTag = false;
            $this -> hasXMLEndTag = true;
            array_push($this -> flowPipe, $flowElement);
			//echo "xml-end ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
           
            return true;
        }
        
        else if($this -> is_xml($this -> semiStatefulDataFlow, $this -> hasXMLStartTag, $this -> hasXml)){
            $flowElement = new Xml($this -> semiStatefulDataFlow);
            $this -> hasXml = true;
            array_push($this -> flowPipe, $flowElement);
			//echo "xml ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
     
            return true;
        }
        
        else if($this -> is_start_start_tag($this -> semiStatefulDataFlow)){
            $flowElement = new StartStartTag($this -> semiStatefulDataFlow);
            $this -> hasStartStartTag = true;
            $this -> hasStartEndTag = false;
            $this -> hasXMLEndTag = false;
            $this -> hasEndEndTag = false;
            array_push($this -> flowPipe, $flowElement);
			//echo "start-start ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
           
            return true;
        }
        
        else if($this -> is_start_end_tag($this -> semiStatefulDataFlow, $this -> hasStartStartTag, $this -> hasTagWord, $this -> hasQuote)){
            $flowElement = new StartEndTag($this -> semiStatefulDataFlow);
            $this -> hasStartEndTag = true;
            $this -> hasStartStartTag = false;
            $this -> hasTagWord = false;
            array_push($this -> flowPipe, $flowElement);
			//echo "start-end ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
        
        else if($this -> is_end_start_tag($this -> semiStatefulDataFlow)){
            $flowElement = new EndStartTag($this -> semiStatefulDataFlow);
            $this -> hasEndStartTag = true;
            $this -> hasStartEndTag = false;
            $this -> hasEndEndTag = false;
            array_push($this -> flowPipe, $flowElement);
			//echo "end-start ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
        
        else if($this -> is_end_end_tag($this -> semiStatefulDataFlow, $this -> hasEndStartTag, $this -> hasTagWord)){
            $flowElement = new EndEndTag($this -> semiStatefulDataFlow);
            $this -> hasEndStartTag = false;
            $this -> hasTagWord = false;
            $this -> hasEndEndTag = true;
            array_push($this -> flowPipe, $flowElement);
			//echo "end-end ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
		
		else if($this -> is_empty_end_tag($this -> semiStatefulDataFlow, $this -> hasStartStartTag, $this -> hasTagWord, $this -> hasQuote)){
            $flowElement = new EmptyEndTag($this -> semiStatefulDataFlow);
            $this -> hasStartStartTag = false;
			$this -> hasTagWord = false;
            $this -> hasEndEndTag = true;
			
            array_push($this -> flowPipe, $flowElement);
			//echo "end-end ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
        
        else if($this -> is_equals($this -> semiStatefulDataFlow, $this -> hasStartStartTag, $this -> hasXMLStartTag,
            $this -> hasTagWord, $this -> hasAttributeTagWord, $this -> hasXml, $this -> hasQuote)){
            $flowElement = new Equals($this -> semiStatefulDataFlow);
            $this -> hasEquals = true;
            array_push($this -> flowPipe, $flowElement);
			//echo "equals ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
        
        else if($this -> is_quote($this -> semiStatefulDataFlow, $this -> hasEquals, $this -> hasStartEndTag, $this -> hasXMLEndTag)){
            $flowElement = new Quote($this -> semiStatefulDataFlow);
            
            if($this -> hasQuote){
                $this -> hasAttributeTagWord = false;
                $this -> hasEquals = false;
                $this -> hasQuote = false;
				$this -> clearCurrentQuote();
            }
            else{
                $this -> hasQuote = true;
				$this -> currentQuote = $this -> semiStatefulDataFlow;
            }
   
            array_push($this -> flowPipe, $flowElement);
			//echo "quote ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
		
		else if($this -> is_escape_start_tag($flow, $this -> hasEscapeStartTag)){
			$flowElement = new EscapeStartTag($this -> semiStatefulDataFlow);
            
            $this -> hasEscapeStartTag = true;
   
            array_push($this -> flowPipe, $flowElement);
			//echo "escapestarttag ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
		}
		
		else if($this -> is_escape_end_tag($flow, $this -> hasEscapeStartTag)){
			
			$remainingEscapeContent = substr($this -> semiStatefulDataFlow, 0, -strlen(ESCAPE_END_TAG));
			
			if(strlen($remainingEscapeContent) > 0){
				$remainingFlowContentElement = new FreeWord($remainingEscapeContent);
				array_push($this -> flowPipe, $remainingFlowContentElement);
				//echo "freeword ".$remainingEscapeContent ."\n";
			}
			
            $flowElement = new EscapeEndTag(ESCAPE_END_TAG);
            $this -> hasEscapeStartTag = false;
			
            array_push($this -> flowPipe, $flowElement);
			//echo "escapeendtag ". ";" ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
		}
		
        else if($this -> is_tagword($this -> semiStatefulDataFlow, $this -> hasStartStartTag, $this -> hasEndStartTag, $this -> hasTagWord)){
            $flowElement = new TagWord($this -> semiStatefulDataFlow);
            $this -> hasTagWord = true;
            array_push($this -> flowPipe, $flowElement);
			//echo "tagword ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
        
        else if($this -> is_attributetagword($this -> semiStatefulDataFlow, $this -> hasStartStartTag, $this -> hasXMLStartTag, 
												$this -> hasTagWord, $this -> hasStartEndTag, $this -> hasXMLEndTag, $this -> hasXml, 
												$this -> hasWhitespace, $this -> hasQuote)){
            $flowElement = new AttributeTagWord($this -> semiStatefulDataFlow);
            $this -> hasAttributeTagWord = true;
			$this -> hasWhitespace = false;
			
            array_push($this -> flowPipe, $flowElement);
			//echo "attributetagword ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();
            
            return true;
        }
		
		else if($this -> appearsToBeAttributeTextFlow($flow, $this -> hasQuote, $this -> hasEscapeStartTag)){
			$flowElement = new FreeWord($this -> semiStatefulDataFlow);
			array_push($this -> flowPipe, $flowElement);
			
			//echo "attributefreeword ".$this -> semiStatefulDataFlow ."\n";
			$this -> clearSemiStatefulDataFlow();
			return true;
		}
        
		else if($this -> appearsToBeTextFlow($this -> semiStatefulDataFlow, $this -> hasCommentStartTag, $this -> hasCDataStartTag, 
											 $this -> hasStartEndTag, $this -> hasEndEndTag, $this -> hasXMLEndTag, 
											 $this -> hasEscapeStartTag
											 )){
			
			/*
				Since the code stepped in here because of no possible future sense, we have a valid 
				current text flow but we do not have a valid future text flow. At this point, 
				if we add this flow as valid free word, the forbidden tag would break up into separate valid free words
				because some parts of the forbidden tag will not be included in the current flow since we catch that in the future senses.
				so we need to check the future flow again against forbidden tag rules in here.
			*/
			if(!$this -> textFlowContainsForbiddenSequence($this -> semiStatefulDataFlowFuture)){
				$flowElement = new FreeWord($this -> semiStatefulDataFlow);
				array_push($this -> flowPipe, $flowElement);
				
				//echo "freeword ".$this -> semiStatefulDataFlow ."\n";
				$this -> clearSemiStatefulDataFlow();
				return true;
			}
        }
		
		else if($this -> is_comment_start_tag($this -> semiStatefulDataFlow, $this -> hasCommentStartTag)){
            $flowElement = new CommentStartTag($this -> semiStatefulDataFlow);
            $this -> hasCommentStartTag = true;

            array_push($this -> flowPipe, $flowElement);
			//echo "comment-start '".$this -> semiStatefulDataFlow ."'\n";
            $this -> clearSemiStatefulDataFlow();

            return true;
        }
		
		else if($this -> is_comment_end_tag($this -> semiStatefulDataFlow, $this -> hasCommentStartTag)){

			$remainingCommentContent = substr($this -> semiStatefulDataFlow, 0, -strlen(COMMENT_END_TAG));
		
			if(!$this -> commentFlowContainsForbiddenSequence($remainingCommentContent)){
				
				if(strlen($remainingCommentContent) > 0){
					$remainingFlowContentElement = new FreeWord($remainingCommentContent);
					array_push($this -> flowPipe, $remainingFlowContentElement);
					//echo "freeword ".$remainingCommentContent ."\n";
				}
				
				$flowElement = new CommentEndTag(COMMENT_END_TAG);
				$this -> hasCommentStartTag = false;
				array_push($this -> flowPipe, $flowElement);
				//echo "comment-end "."-->" ."\n";
				$this -> clearSemiStatefulDataFlow();

				return true;
			}
        }
		
		else if($this -> is_cdata_start_tag($this -> semiStatefulDataFlow, $this -> hasCDataStartTag)){
          
			$flowElement = new CDataStartTag($this -> semiStatefulDataFlow);
            $this -> hasCDataStartTag = true;

            array_push($this -> flowPipe, $flowElement);
			//echo "cdata-start ".$this -> semiStatefulDataFlow ."\n";
            $this -> clearSemiStatefulDataFlow();

            return true;
        }
		
		else if($this -> is_cdata_end_tag($this -> semiStatefulDataFlow, $this -> hasCDataStartTag)){

			$remainingCDataContent = substr($this -> semiStatefulDataFlow, 0, -strlen(CDATA_END_TAG));
		
			if(!$this -> cDataFlowContainsForbiddenSequence($remainingCDataContent)){
				
				if(strlen($remainingCDataContent) > 0){
					$remainingFlowContentElement = new FreeWord($remainingCDataContent);
					array_push($this -> flowPipe, $remainingFlowContentElement);
					//echo "freeword ".$remainingCDataContent ."\n";
				}
				
				$flowElement = new CDataEndTag(CDATA_END_TAG);
				$this -> hasCDataStartTag = false;
				array_push($this -> flowPipe, $flowElement);
				//echo "cdata-end "."]]>" ."\n";
				$this -> clearSemiStatefulDataFlow();

				return true;
			}
        }

		//echo ".." . $this -> semiStatefulDataFlow . "..\n";
        return false;
    }
    
    /////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////
    /////////////////// End of Data Flow Sensing ////////////////////
    /////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////
    
    private function futureSensePossible($flow){

		if($this -> appearsToBeCommentStartTag($flow, $this -> hasCommentStartTag)){
            return true;
        }
		
		else if($this -> appearsToBeCommentEndTag($flow, $this -> hasCommentStartTag)){
            return true;
        }
		
		else if($this -> appearsToBeCommentFlow($flow, $this -> hasCommentStartTag)){
            return true;
        }
		
		else if($this -> appearsToBeCDataStartTag($flow, $this -> hasCDataStartTag)){
            return true;
        }
		
		else if($this -> appearsToBeCDataEndTag($flow, $this -> hasCDataStartTag)){
            return true;
        }
		
		else if($this -> appearsToBeCDataFlow($flow, $this -> hasCDataStartTag)){
            return true;
        }
		
		else if($this -> needToCheckForOtherFlowElementDefinitions()){
			return $this -> futureSensePossibleForOtherFlowElementDefinitions($flow);
		}
		
        return false;
    }
	
	private function needToCheckForOtherFlowElementDefinitions(){
		return !$this -> hasCommentStartTag && !$this -> hasCDataStartTag; 
	}
	
	private function futureSensePossibleForOtherFlowElementDefinitions($flow){
		
		if($this -> is_xml_start_tag($flow)){
			return true;
		}
		
		else if($this -> is_xml_end_tag($flow, $this -> hasXml, $this -> hasQuote)){
			return true;
		}
		
		else if($this -> is_xml($flow, $this -> hasXMLStartTag, $this -> hasXml)){
			
			return true;
		}
		
		else if($this -> is_start_start_tag($flow)){
			return true;
		}
		
		else if($this -> is_start_end_tag($flow, $this -> hasStartStartTag, $this -> hasTagWord, $this -> hasQuote)){
			return true;
		}
		
		else if($this -> is_end_start_tag($flow)){
			return true;
		}
		
		else if($this -> is_end_end_tag($flow, $this -> hasEndStartTag, $this -> hasTagWord)){
			return true;
		}
		
		else if($this -> is_empty_end_tag($flow, $this -> hasStartStartTag, $this -> hasTagWord, $this -> hasQuote)){
			return true;
		}
		
		else if($this -> is_equals($flow, $this -> hasStartStartTag, $this -> hasXMLStartTag, $this -> hasTagWord, $this -> hasAttributeTagWord, $this -> hasXml, $this -> hasQuote)){
			return true;
		}
		
		else if($this -> is_quote($flow, $this -> hasEquals, $this -> hasStartEndTag, $this -> hasXMLEndTag)){
			return true;
		}
		
		else if($this -> is_escape_start_tag($flow, $this -> hasEscapeStartTag)){
			return true;
		}
		
		else if($this -> is_escape_end_tag($flow, $this -> hasEscapeStartTag)){
			return true;
		}
		
		else if($this -> is_escapeword($flow, $this -> hasEscapeStartTag)){
			return true;
		}
		
		else if($this -> is_tagword($flow, $this -> hasStartStartTag, $this -> hasEndStartTag, $this -> hasTagWord)){
			return true;
		}
		
		else if($this -> is_attributetagword($flow, $this -> hasStartStartTag, $this -> hasXMLStartTag, 
													$this -> hasTagWord, $this -> hasStartEndTag, $this -> hasXMLEndTag, 
													$this -> hasXml, $this -> hasWhitespace, $this -> hasQuote)){
			return true;
		}
		
		else if($this -> appearsToBeAttributeTextFlow($flow, $this -> hasQuote, $this -> hasEscapeStartTag)){
			return true;
		}
		
		else if($this -> appearsToBeTextFlow($flow, $this -> hasCommentStartTag, $this -> hasCDataStartTag, $this -> hasStartEndTag, 
													$this -> hasEndEndTag, $this -> hasXMLEndTag, $this -> hasEscapeStartTag
													)){
			return true;
		}
		
		return false;
	}
    
    private function isFlowCompliesWithTagwordNamingStandard($flow){
        $splittedWord = str_split($flow);
        
		$flowLength = count($splittedWord);
		
		for ($i = 0; $i < $flowLength; $i++){
			$chr = $splittedWord[$i];
			$asciiCodeOfFlow = ord($chr);
			
			$valid = $this -> validToBeNameOfTheTagword($asciiCodeOfFlow, $i);
			
			if(!$valid){
				return false;
			}
		}
		
        return true;
    }

	private function validToBeNameOfTheTagword($asciiCodeOfFlow, $index){
		
		if($index === 0){
			foreach(VALID_START_CHAR_CODE_RANGES as $range){
				if($asciiCodeOfFlow >= $range -> start && $asciiCodeOfFlow <= $range -> end){
					return true;
				}
			}
		}
		else{
			foreach(VALID_NAME_CHAR_CODE_RANGES as $range){
				if($asciiCodeOfFlow >= $range -> start && $asciiCodeOfFlow <= $range -> end){
					return true;
				}
			}
		}

		return false;
	}
    
    private function flowValidToBeAttributeValue($flow){
		return 
			   strpos($flow, $this -> currentQuote) === false
			&& strpos($flow, START_START_TAG) === false
			&& strpos($flow, END_START_TAG) === false
			&& strpos($flow, ESCAPE_START_TAG) === false;
    }
    
	//this method is for general check up of incoming text flow that must not contain certain flow starting tags or forbidden sequences
    private function flowValidToBeTextFlow($flow){
		
        return  strpos($flow, START_START_TAG) === false 
             && strpos($flow, END_START_TAG) === false
			 && strpos($flow, ESCAPE_START_TAG) === false
			 && !$this -> textFlowContainsForbiddenSequence($flow);
    }

	private function textFlowContainsForbiddenSequence($flow){
		return strpos($flow, CDATA_END_TAG) !== false;
	}
	
	private function commentFlowContainsForbiddenSequence($flow){
		return strpos($flow, DOUBLE_HYPEN) !== false
			|| substr($flow, -1) === "-";
	}
	
	private function cDataFlowContainsForbiddenSequence($flow){
		return strpos($flow, CDATA_END_TAG) !== false;
	}
	
    private function clearSemiStatefulDataFlow(){
        $this -> semiStatefulDataFlow = EMPTY_STRING;
    }
	
	private function clearSemiStatefulDataFlowFuture(){
        $this -> semiStatefulDataFlowFuture = EMPTY_STRING;
    }
	
	private function clearCurrentQuote(){
        $this -> currentQuote = EMPTY_STRING;
    }
	
    private function isPipeEmpty(){
        return count($this -> flowPipe) === 0;
    }
  
	private function appearsToBeTextFlow($flow, $hasCommentStartTag, $hasCDataStartTag, $hasStartEndTag, $hasEndEndTag, 
												$hasXMLEndTag, $hasEscapeStartTag
												){
		return !$hasCommentStartTag
			&& !$hasCDataStartTag
			&& !$hasEscapeStartTag
			&& !$hasXMLEndTag
			&& ($hasStartEndTag || $hasEndEndTag/* || $hasXMLEndTag*/)
			&& $this -> flowValidToBeTextFlow($flow);
    }
	
	private function appearsToBeAttributeTextFlow($flow, $hasQuote, $hasEscapeStartTag){
		return !$hasEscapeStartTag && $hasQuote && $this -> flowValidToBeAttributeValue($flow);
    }
	
	private function appearsToBeCommentFlow($flow, $hasCommentStartTag){

		/*if($hasCommentStartTag){
			
			$commentEndTagLength = strlen(COMMENT_END_TAG);
			$flowDataLength = strlen($flow);

			$i = 0;
			$k = max($flowDataLength - strlen(COMMENT_END_TAG), 0);
	
			for( ; $i < $commentEndTagLength && $k < $flowDataLength; $i++, $k++){
				if($flow[$k] !== COMMENT_END_TAG[$i]){
					break;
				}
			}

			if(strpos($flow, DOUBLE_HYPEN) !== false){
				return false;
			}
			else if($flowDataLength < strlen(COMMENT_END_TAG)){
				return true;
			}
			else if($i === $commentEndTagLength){
				return true;
			}
			else if(strpos($flow, COMMENT_END_TAG) === false){
				return true;
			}
		}

		return false;*/
		
		return $hasCommentStartTag 
			&& strpos($flow, DOUBLE_HYPEN) === false
			&& !$this -> appearsToBeCommentEndTag($flow, $hasCommentStartTag)
			&& strpos($flow, COMMENT_END_TAG) === false;
    }
	
	private function appearsToBeCDataFlow($flow, $hasCDataStartTag){

		/*if($hasCDataStartTag){
			
			$cdataEndTagLength = strlen(CDATA_END_TAG);
			$flowDataLength = strlen($flow);
			
			$i = 0;
			$k = max($flowDataLength - strlen(CDATA_END_TAG), 0);
	
			for( ; $i < $cdataEndTagLength && $k < $flowDataLength; $i++, $k++){
				if($flow[$k] !== CDATA_END_TAG[$i]){
					break;
				}
			}

			if($flowDataLength < strlen(CDATA_END_TAG)){
				return true;
			}
			else if($i === $cdataEndTagLength){
				return true;
			}
			else if(strpos($flow, CDATA_END_TAG) === false){
				return true;
			}
		}

		return false;*/
		
		return $hasCDataStartTag
			&& !$this -> appearsToBeCDataEndTag($flow, $hasCDataStartTag)
			&& strpos($flow, CDATA_END_TAG) === false;	
    }
	
	private function internalFinalize(){
		$this -> hasCDataStartTag = false;
		$this -> hasEscapeStartTag = false;
		$this -> hasCommentStartTag = false;
		$this -> hasStartStartTag = false;
		$this -> hasEndStartTag = false;
		$this -> hasEndEndTag = false;
		$this -> hasStartEndTag = false;
		$this -> hasXMLStartTag = false;
		$this -> hasXMLEndTag = false;
		$this -> hasXml = false;
		$this -> hasTagWord = false;
		$this -> hasAttributeTagWord = false;
		$this -> hasEquals = false;
		$this -> hasQuote = false;
		$this -> hasWhitespace = false;
		
		$this -> clearCurrentQuote();
		
		if(!$this -> isSemiStatefulDataFlowEmpty()){
			$flowElement = new UnreasonableWord($this -> semiStatefulDataFlow);
			array_push($this -> flowPipe, $flowElement);
            
			//echo "unreasonableword ".$this -> semiStatefulDataFlow ."\n";
			$this -> clearSemiStatefulDataFlow();
		}
		
		$this -> clearSemiStatefulDataFlowFuture();
	}
	
	public function finalize(){
        unset($this -> flowPipe);
		
		$this -> flowPipe = array();
    }
	
	public function isSemiStatefulDataFlowEmpty(){
        return strlen($this -> semiStatefulDataFlow) === 0;
    }
 
    public function getFlowPipe(){
        return $this -> flowPipe;
    }
	
	public function printSemiStatefulDataFlow(){
		echo "semistatefuldataflow length: [" . strlen($this -> semiStatefulDataFlow) . "]\n";
		echo "semistatefuldataflow : [". $this -> semiStatefulDataFlow ."]\n";
		echo "-----------------\n";
	}
}

?>