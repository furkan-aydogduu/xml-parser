<?php 
namespace org\xmlparser\utilities\console;

use DateTimeImmutable;

class Console{
    
    private $queue;
    
    public function __construct(){
        $this -> queue = array();
    }
	
	public function add($output){
		
		$loggingTime = new DateTimeImmutable();
		
		$output = "[ " . $loggingTime -> format("d.m.Y H:i:s") . " ]: " . $output;
		
		if(count($this -> queue) >= 10){
			array_splice($this -> queue, 0, 1);
		}
		
		array_push($this -> queue, $output);
		
		return $loggingTime;
	}
	
	public function print(){
		
		echo "\e[H\e[J";
		
		$queueLength = count($this -> queue);
		
		for($i = 0; $i < $queueLength; $i++){
			echo $this -> queue[$i] . PHP_EOL;
		}
	}
	
	public function clear(){
		unset($this -> queue);
		
		$this -> queue = array();
	}
}
?>