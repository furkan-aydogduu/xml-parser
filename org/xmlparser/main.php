<?php
namespace org\xmlparser\parser;

require_once dirname(__FILE__) . "/parser/xml_parser.php";

require_once dirname(__ROOT__) . "/utilities/reader/xml_file_reader.php";

use org\xmlparser\utilities\reader\XMLFileReader;

/////////////////////////////// MAIN ////////////////////////////////////

class Main {
    
	private static $rootTestCasesFolder;
	private static $failedTestCasesFolder;
	
    public static function main ($args){

		Main::$rootTestCasesFolder = dirname(__ROOT__) . "/test/test-cases/";
		Main::$failedTestCasesFolder = Main::$rootTestCasesFolder . "failed/";
		
		$xmlInputFile = $args[1];
		
		
		if(file_exists(Main::$rootTestCasesFolder . $xmlInputFile)){
			$xmlInputFile = Main::$rootTestCasesFolder . $xmlInputFile;
		}
		else{
			$xmlInputFile = Main::$failedTestCasesFolder . $xmlInputFile;
		}

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
