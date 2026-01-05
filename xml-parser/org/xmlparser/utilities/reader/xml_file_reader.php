<?php 
namespace org\xmlparser\utilities\reader;

class XMLFileReader{
    
    private $xmlInputFile;
    
    public function __construct($xmlInputFile){
        $this -> xmlInputFile = $xmlInputFile;
    }
    
    public function readXMLFileAsString(){
		
		if(!file_exists($this -> xmlInputFile)){
			echo "Error: input file does not exist: " . $this -> xmlInputFile . PHP_EOL;
			return false;
		}
		
        $xmlInputAsString = file_get_contents($this -> xmlInputFile);
		
        return $xmlInputAsString;
    }
}
?>