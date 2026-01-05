<?php
namespace org\xmlparser\test;

require_once dirname(__FILE__) . "/abstract_test_main.php";

require_once dirname(__TESTROOT__) . "/utilities/writer/xml_file_writer.php";
require_once __TESTROOT__ . "/external/http/http_request_builder.php";

use org\xmlparser\test\external\http\HTTPHeader;
use org\xmlparser\test\external\http\HTTPRequestParam;
use org\xmlparser\test\external\http\HTTPRequestBuilder;
use org\xmlparser\test\external\http\HTTPRequest;
use org\xmlparser\utilities\writer\XMLFileWriter;
use org\xmlparser\utilities\console\Console;
use org\xmlparser\parser\XMLParser;

class TestMainByGenerator extends Main {
    private static $interval = 2;  //seconds
	
	private static $failedTestCasesDir;
	
	private static $http;
	private static $xmlGeneratorHost;
	private static $xmlGeneratorPort;
	private static $xmlGeneratorUrl;
	private static $xmlGeneratorRequestMethod;
	
	private static array $xmlGeneratorRequestHeaders;
	private static array $xmlGeneratorRequestParams;
	
	public static function initTestCase(){
		
		TestMainByGenerator::$interval = 2;  //seconds
	
		TestMainByGenerator::$failedTestCasesDir = __TESTROOT__ . "/test-cases/failed";
	
		TestMainByGenerator::$http = "http";
		TestMainByGenerator::$xmlGeneratorHost = "localhost";
		TestMainByGenerator::$xmlGeneratorPort = 3000;
		TestMainByGenerator::$xmlGeneratorUrl = "get-random-xml";
		TestMainByGenerator::$xmlGeneratorRequestMethod = "GET";
		
		TestMainByGenerator::$xmlGeneratorRequestHeaders = array(
			new HTTPHeader("Accept", "text/xml"),
			new HTTPHeader("Cache-Control", "no-cache")
		);
		
		TestMainByGenerator::$xmlGeneratorRequestParams = array(
			new HTTPRequestParam("maxDepth", 25),
			new HTTPRequestParam("maxElementCountPerBranch", 35),
			new HTTPRequestParam("maxStringLength", 100),
			new HTTPRequestParam("random", "true")
		);
	}
	
    public static function main ($args){
		
		TestMainByGenerator::initTestCase();
		
		if(!is_dir(TestMainByGenerator::$failedTestCasesDir)){
			mkdir(TestMainByGenerator::$failedTestCasesDir);
		}
							
		$console = new Console();
		
		$requestBuilder = new HTTPRequestBuilder(
										TestMainByGenerator::$http, 
										TestMainByGenerator::$xmlGeneratorHost, 
										TestMainByGenerator::$xmlGeneratorPort, 
										TestMainByGenerator::$xmlGeneratorRequestMethod, 
										TestMainByGenerator::$xmlGeneratorUrl,
										TestMainByGenerator::$xmlGeneratorRequestHeaders,
										TestMainByGenerator::$xmlGeneratorRequestParams
										);
										
		$request = $requestBuilder -> build();
		
		$isRequestSuccessfullyInitiated = $request -> init();
		
		if($isRequestSuccessfullyInitiated){
			$xmlParser = new XMLParser();
		
			$xmlFileWriter = new XMLFileWriter();
			
			while(true){
			
				$generatedXMLAsString = $request -> doRequest();
				
				if($generatedXMLAsString !== false && strlen($generatedXMLAsString) > 0){
					
					$xmlParser -> setXMLInput($generatedXMLAsString);
				
					$xmlDocument = $xmlParser -> parseXMLAndConvertToDocument();
					
					if($xmlDocument !== null){
						
						$totalNodeCount = $xmlParser -> getNodeCount($xmlDocument);
						
						$logMessage = "Test case is valid! [ Total Node Count: " . $totalNodeCount . " ]";
						
						$console -> add($logMessage);
					}
					else{
						
						$logMessage = "Error in test case!";
						
						$loggingTime = $console -> add($logMessage);

						$failedTestInputFileName = TestMainByGenerator::$failedTestCasesDir . "/" 
													. "failed_test_input_"
													. $loggingTime -> format("d_m_Y_H_i_s") 
													. ".xml";
						
						$xmlFileWriter -> setFileName($failedTestInputFileName);
						
						$xmlFileWriter -> writeXMLToFile($generatedXMLAsString);
					}
				}
				else{
					$logMessage =  "Incorrect test case from server or network error!";
						
					$console -> add($logMessage);
				}
				
				$console -> print();
				
				sleep(TestMainByGenerator::$interval);
			}
		}
		else{
			$logMessage = "Request initialization error!";
				
			$console -> add($logMessage);
		}
		
		$console -> print();
		
		$console -> clear();
    }
	
	
}

?>