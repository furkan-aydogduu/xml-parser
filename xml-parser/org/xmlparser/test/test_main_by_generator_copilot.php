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

class TestMainByGeneratorCopilot extends Main {
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
		
		TestMainByGeneratorCopilot::$interval = 2;  //seconds
	
		TestMainByGeneratorCopilot::$failedTestCasesDir = __TESTROOT__ . "/test-cases/failed";
	
		TestMainByGeneratorCopilot::$http = "http";
		TestMainByGeneratorCopilot::$xmlGeneratorHost = "localhost";
		TestMainByGeneratorCopilot::$xmlGeneratorPort = 3001;
		TestMainByGeneratorCopilot::$xmlGeneratorUrl = "get-random-xml-copilot";
		TestMainByGeneratorCopilot::$xmlGeneratorRequestMethod = "GET";
		
		TestMainByGeneratorCopilot::$xmlGeneratorRequestHeaders = array(
			new HTTPHeader("Accept", "text/xml"),
			new HTTPHeader("Cache-Control", "no-cache")
		);
	}
	
    public static function main ($args){
		
		TestMainByGeneratorCopilot::initTestCase();
		
		if(!is_dir(TestMainByGeneratorCopilot::$failedTestCasesDir)){
			mkdir(TestMainByGeneratorCopilot::$failedTestCasesDir);
		}
							
		$console = new Console();
		
		$requestBuilder = new HTTPRequestBuilder(
										TestMainByGeneratorCopilot::$http, 
										TestMainByGeneratorCopilot::$xmlGeneratorHost, 
										TestMainByGeneratorCopilot::$xmlGeneratorPort, 
										TestMainByGeneratorCopilot::$xmlGeneratorRequestMethod, 
										TestMainByGeneratorCopilot::$xmlGeneratorUrl,
										TestMainByGeneratorCopilot::$xmlGeneratorRequestHeaders
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
						
						$logMessage = "Copilot test case is valid! [ Total Node Count: " . $totalNodeCount . " ]";
						
						$console -> add($logMessage);
					}
					else{
						
						$logMessage = "Error in copilot test case!";
						
						$loggingTime = $console -> add($logMessage);

						$failedTestInputFileName = TestMainByGeneratorCopilot::$failedTestCasesDir . "/" 
													. "failed_copilot_test_input_"
													. $loggingTime -> format("d_m_Y_H_i_s") 
													. ".xml";
						
						$xmlFileWriter -> setFileName($failedTestInputFileName);
						
						$xmlFileWriter -> writeXMLToFile($generatedXMLAsString);
					}
				}
				else{
					$logMessage =  "Incorrect copilot test case from server or network error!";
						
					$console -> add($logMessage);
				}
				
				$console -> print();
				
				sleep(TestMainByGeneratorCopilot::$interval);
			}
		}
		else{
			$logMessage = "Copilot request initialization error!";
				
			$console -> add($logMessage);
		}
		
		$console -> print();
		
		$console -> clear();
    }
}

?>