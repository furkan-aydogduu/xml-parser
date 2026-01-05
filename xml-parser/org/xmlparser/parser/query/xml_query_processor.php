<?php 
namespace org\xmlparser\parser\query;

/**
 * 
 * This is a simple XPath Query processor.
 * Since the project only focuses on company category queries, I kept it simple. It could be more talented.
 * 
 * usage : /<tag_name>/<tag_name>/...
 * e.g.    /contacts/contact                    (this will return all the contacts in the xml document as array)
 * e.g.    /contacts/contact/clientCategory     (this will return all the clientCategories in the xml document as array) 
 * 
 * Note : when you do an XML query on a node (no matter if it is root or not), 
 *        make sure your xpath query starts with that node name. 
 *        So if your query is "/contact/clientCategory", make sure that the node you give to the processor is a "contact" node
 * */

require_once __ROOT__ . "/query/xml_query.php";

class XMLQueryProcessor {
    
    private $queryDelimiter = '/';
   
    public function doXMLQuery($xpathQuery, $node){
        
        $parsedQuery = $this -> parseQuery($xpathQuery);
        
        if($parsedQuery !== NULL){
            $collectedNodes = array();
            $this -> collectComplyingNodesByQuery($parsedQuery, $node, $collectedNodes);
            return $collectedNodes;
        }
        else{
            return NULL;
        }
    }
    
    /*
     * This method checks & parses the given xpath query and turns it into a linked list xml query (XMLQuery)
     * and returns the first element of the query. If the given query is not valid, it returns NULL
     * */
    private function parseQuery($xpathQuery){
        
        if($this -> isQueryValid($xpathQuery)){
           
            $splittedQuery = explode($this -> queryDelimiter, $xpathQuery);
            $cleansedSplittedQuery = array_diff($splittedQuery, array(""));
            
            $legacyParsedQuery = new XMLQuery();
            $parsedQuery = $legacyParsedQuery;
            
            $i = 0;
            $querySize = count($cleansedSplittedQuery);
            
            foreach ($cleansedSplittedQuery as $subQuery){
                $parsedQuery -> setValue($subQuery);
                if($i + 1 < $querySize){
                    $nextQuery = new XMLQuery();
                    $parsedQuery -> setNext($nextQuery);
                    $parsedQuery = $parsedQuery -> getNext();
                }
                $i++;
            }
            return $legacyParsedQuery;
        }
        return NULL;
    }
    
    private function isQueryValid($query){
        
        if(strlen($query) === 0){
            return false;
        }
        
        $splittedQueryChars = str_split($query);
        
        foreach ($splittedQueryChars as $chr){
            $asciiCodeOfChar = ord($chr);
            if(!($asciiCodeOfChar >= 48 && $asciiCodeOfChar <=57)
                && !($asciiCodeOfChar >= 65 && $asciiCodeOfChar <= 90)
                && !($asciiCodeOfChar >= 97 && $asciiCodeOfChar <= 122)
                && $asciiCodeOfChar !== 46 && $asciiCodeOfChar !== 47){
                    return false;
            }
        }
        
        return true;
    }
    
    /*
     * This method collects the results of the parsed query into the $collectedNodes parameter
     * */
    private function collectComplyingNodesByQuery($parsedQuery, $node, &$collectedNodes){
        if($parsedQuery === NULL || $node === NULL) return;
        
        if($node -> getLabel() === $parsedQuery -> getValue()){
            
            if($parsedQuery -> getNext() === NULL){
                array_push($collectedNodes, $node);
            }
            
            foreach ($node -> getSubNodes() as $subNode){
                $this -> collectComplyingNodesByQuery($parsedQuery -> getNext(), $subNode, $collectedNodes);
            }
        }
        else{
            foreach ($node -> getSubNodes() as $subNode){
                $this -> collectComplyingNodesByQuery($parsedQuery, $subNode, $collectedNodes);
            }
        }
        
    }
}

?>