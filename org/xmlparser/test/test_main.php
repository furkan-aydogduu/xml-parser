<?php
namespace org\xmlparser\test;

require_once dirname(__FILE__) . "/abstract_test_main.php";

require_once dirname(__TESTROOT__) . "/utilities/reader/xml_file_reader.php";

use org\xmlparser\utilities\reader\XMLFileReader;
use org\xmlparser\parser\XMLParser;

/////////////////////////////// MAIN ////////////////////////////////////

class TestMain extends Main {
    
	private static $rootTestCasesFolder;
	private static $failedTestCasesFolder;
	
	public static function initTestCase(){
		TestMain::$rootTestCasesFolder = __TESTROOT__ . "/test-cases/";
		TestMain::$failedTestCasesFolder = TestMain::$rootTestCasesFolder . "failed/";
	}
	
    public static function main ($args){
		
		TestMain::initTestCase();
		
		$xmlInputFile = $args[1];
		
		if(file_exists(TestMain::$rootTestCasesFolder . $xmlInputFile)){
			$xmlInputFile = TestMain::$rootTestCasesFolder . $xmlInputFile;
		}
		else{
			$xmlInputFile = TestMain::$failedTestCasesFolder . $xmlInputFile;
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