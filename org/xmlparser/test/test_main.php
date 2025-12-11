<?php
namespace org\xmlparser\test;

require_once dirname(dirname(__FILE__)) . "/utilities/reader/xml_file_reader.php";
require_once dirname(dirname(__FILE__)) . "/output/xmlparser__v1_1.phar";

use org\xmlparser\utilities\reader\XMLFileReader;
use org\xmlparser\parser\XMLParser;

/////////////////////////////// MAIN ////////////////////////////////////

class Main {
    
    public static function main ($args){

		$xmlInputFile = $args[1];
		
		$xmlInputFile = dirname(__FILE__) . "/test-cases/" . $xmlInputFile;

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