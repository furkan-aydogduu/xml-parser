<?php 
namespace org\xmlparser\utilities\writer;

class XMLFileWriter{
    
    private $xmlInputFileName;
    
    public function __construct($xmlInputFileName = ""){
        $this -> xmlInputFileName = $xmlInputFileName;
    }
 
    public function writeXMLToFile($data){
		
		if($this -> xmlInputFileName === null 
			|| gettype($this -> xmlInputFileName) !== "string" 
			&& strlen($this -> xmlInputFileName) < 1 ){
				echo "Error: input file name must be of a valid string type: " . $this -> xmlInputFileName . PHP_EOL;
			return false;
		}
		
		$filePointer = fopen($this -> xmlInputFileName, "w");
		
		if($filePointer){
			$isWritten = fwrite($filePointer, $data);
			
			if(!$isWritten){
				fclose($filePointer);
				echo "Error: unable to write to file: " . $this -> xmlInputFileName . PHP_EOL;
				return false;
			}
		}
		else{
			echo "Error: unable to open file for writing: " . $this -> xmlInputFileName . PHP_EOL;
			return false;
		}
		
		fclose($filePointer);
		
        return true;
    }
	
	public function setFileName($fileName){
		$this -> xmlInputFileName = $fileName;
	}
}
?>