<?php
namespace org\xmlparser\test\external\http;

class HTTPRequest {
    
	private $connection;
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
	
    public function getParameter($paramName){
		
		$parameterCount = count($this -> parameters);
			
		for($i = 0; $i < $parameterCount; $i++){
			$parameter = $this -> parameters[$i];
			
			if($parameter -> getParamName() === $paramName){
				return $parameter;
			}	
		}
		
		return null;
    }
	
	public function setData($data){
		$this -> data = $data;
	}
	
	public function init(){
		
		$this -> connection = curl_init($this -> url);
		
		if($this -> connection === false){
			echo "'" . $this -> url . "'\n";
			return false;
		}
		
		curl_setopt($this -> connection, CURLOPT_TIMEOUT, 5);
		
		curl_setopt($this -> connection, CURLOPT_CONNECTTIMEOUT, 5);
		
		//curl_setopt($this -> connection, CURLOPT_LOCALPORT, $this -> port);
		
		curl_setopt($this -> connection, CURLOPT_CUSTOMREQUEST, $this -> method);
		
		if($this -> method === "POST"){
			curl_setopt($this -> connection, CURLOPT_POST, true);
			curl_setopt($this -> connection, CURLOPT_POSTFIELDS, $this -> data);
		}
		 
		curl_setopt($this -> connection, CURLOPT_RETURNTRANSFER, true);
		
		$headerCount = count($this -> headers);
		
		if($headerCount > 0){
			$curlFormattedHeaders = array();
		
			for($i = 0; $i < $headerCount; $i++){
				$header = $this -> headers[$i];

				array_push($curlFormattedHeaders, $header -> getHeaderName() . ": " . $header -> getHeaderValue());
			}
			
			curl_setopt($this -> connection, CURLOPT_HTTPHEADER, $curlFormattedHeaders);
		}

		return true;
	}
	
	public function doRequest(){
		
		$response = null;
		
		if($this -> connection !== false){
			$response = curl_exec($this -> connection);
		}
		else{
			echo "Error: call HTTPRequest -> init() before sending a request!\n";
		}
		
		return $response;
	}
}

?>