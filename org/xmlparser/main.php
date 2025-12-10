<?php
namespace org\xmlparser\parser;

require_once dirname(__FILE__) . "/utilities/reader/xml_file_reader.php";
require_once dirname(__FILE__) . "/parser/xml_parser.php";

use org\xmlparser\utilities\reader\XMLFileReader;

/////////////////////////////// MAIN ////////////////////////////////////

class Main {
    
    public static function main ($args){

		$xmlInputFile = $args[1];
		
		$xmlInputFile = dirname(__FILE__) . "/test/test-cases/" . $xmlInputFile;

		$xmlFileReader = new XMLFileReader($xmlInputFile);
		$xmlInputAsString = $xmlFileReader -> readXMLFileAsString();
		
		if($xmlInputAsString !== false){
			$xmlParser = new XMLParser($xmlInputAsString);
			$xmlDocument = $xmlParser -> parseXMLAndConvertToDocument();
		}
		else{
			return;
		}
		
		if($xmlDocument !== null){
			echo "XML Document is valid!";
		}
		else{
			echo "Error in XML Document!";
		}
    }
}
?>
