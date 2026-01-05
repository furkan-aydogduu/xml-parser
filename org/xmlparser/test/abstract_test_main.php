<?php
namespace org\xmlparser\test;

define('__TESTROOT__', dirname(__FILE__));

require_once dirname(__TESTROOT__) . "/output/xmlparser__v1_2.phar";

require_once dirname(__TESTROOT__) . "/utilities/console/console.php";

abstract class Main {
    
    public abstract static function initTestCase();
}

?>