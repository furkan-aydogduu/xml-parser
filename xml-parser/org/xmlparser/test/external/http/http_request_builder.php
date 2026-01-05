<?php
namespace org\xmlparser\test\external\http;

require_once __TESTROOT__ . "/external/http/http_request_param.php";
require_once __TESTROOT__ . "/external/http/http_request.php";
require_once __TESTROOT__ . "/external/http/http_header.php";

class HTTPRequestBuilder {
    
	private $http;
	private $host;
	private $port;
	private $method;
	private $url;
	private array $headers;
	private array $parameters;
	private $data;
	
	public function __construct($http, $host, $port, $method, $url, array $headers = array(), array $parameters = array(), $data = ""){
		$this -> http = $http;
		$this -> host = $host;
		$this -> port = $port;
        $this -> method = $method;
		$this -> url = $url;
		$this -> headers = $headers;
		$this -> parameters = $parameters;
		$this -> data = $data;
    }
	
	public function addParameter($requestParameter){
		
		if(is_a($requestParameter, HTTPRequestParam::class)){
			$paramName = $requestParameter -> getParamName();
			$paramValue = $requestParameter -> getParamValue();
			
			if($paramName !== null && gettype($paramName) === "string" && strlen($paramName) > 0){
				array_push($this -> parameters, $requestParameter);
			}
		}
	}
	
	public function setParameter($paramName, $paramValue){
		
		if($paramName !== null && gettype($paramName) === "string" && strlen($paramName) > 0){
			$parameterModified = false;
			
			$parameterCount = count($this -> parameters);
			
			for($i = 0; $i < $parameterCount; $i++){
				$parameter = $this -> parameters[$i];
				
				if($parameter -> getParamName() === $paramName){
					$parameter -> setParamValue($paramValue);
					$parameterModified = true;
				}
			}
			
			if(!$parameterModified){
				$newParameter = new HTTPRequestParam($paramName, $paramValue);
				array_push($this -> parameters, $newParameter);
			}
			
			if($paramName !== null && gettype($paramName) === "string" && strlen($paramName) > 0){
				array_push($this -> parameters, $requestParameter);
			}
		}
	}
	
	public function addHeader($requestHeader){
		
		if(is_a($requestHeader, HTTPHeader::class)){
			$headerName = $requestHeader -> getHeaderName();
			$headerValue = $requestHeader -> getHeaderValue();
			
			if($headerName !== null && gettype($headerName) === "string" && strlen($headerName) > 0){
				array_push($this -> headers, $requestHeader);
			}
		}
	}
	
	public function setHeader($headerName, $headerValue){
			
		if($headerName !== null && gettype($headerName) === "string" && strlen($headerName) > 0){
			$headerModified = false;
			
			$headerCount = count($this -> headers);
			
			for($i = 0; $i < $headerCount; $i++){
				$header = $this -> headers[$i];
				
				if($header -> getHeaderName() === $headerName){
					$header -> setHeaderValue($headerValue);
					$headerModified = true;
				}
			}
			
			if(!$headerModified){
				$newHeader = new HTTPHeader($headerName, $headerValue);
				array_push($this -> headers, $newHeader);
			}
		}
	}
	
	public function setData($data){
		$this -> data = $data;
	}
	
	public function build(){
		$builtURL = $this -> buildURL();
		
		$request = new HTTPRequest($this -> http, 
									$this -> host, 
									$this -> port, 
									$this -> method, 
									$builtURL, 
									$this -> headers,
									$this -> parameters, 
									$this -> data
									);
		return $request;
	}
	
	public function buildURL(){
		
		$url = $this -> http . "://" . $this -> host . ":" . $this -> port . '/' . $this -> url;
		
		if($this -> method === "GET"){

			$parameterCount = count($this -> parameters);
			
			if($parameterCount > 0){
				$url = $url . "?";
				
				for($i = 0; $i < $parameterCount; $i++){
					$parameter = $this -> parameters[$i];
					
					$url = $url . $parameter -> getParamName() . "=" . $parameter -> getParamValue();
					
					if($i + 1 < $parameterCount){
						$url = $url . "&";
					}
				}
			}
		}
		
		return $url;
	}
}

?>