<?php 
namespace org\xmlparser\parser\query;

class XMLQuery {
   private $value;
   private $next;
   
   public function __construct(){
       
   }
   
   public function getValue(){
       return $this -> value;
   }
   
   public function &getNext(){
       return $this -> next;
   }
   
   public function setValue($value){
       return $this -> value = $value;
   }
   
   public function setNext($next){
       return $this -> next = $next;
   }
   
}

?>