<?php

require_once dirname(__FILE__) . "/parser/xml_parser.php";

try
{
	$error = false;
	$errorMessages = array();
	
	if(!defined("LIBRARY_NAME")){
		array_push($errorMessages, "LIBRARY_NAME is not defined!");
		$error = true;
	}
	
	if(!defined("LIBRARY_VERSION")){
		array_push($errorMessages, "LIBRARY_VERSION is not defined!");
		$error = true;		
	}

	if($error){
		foreach($errorMessages as $errorMessage){
			echo "Error in library build: " . $errorMessage . PHP_EOL;
		}
		exit;
	}
	
	$formattedLibraryName = strtolower(trim(str_replace(".", "_", LIBRARY_NAME)));
	$formattedVersion = strtolower(trim(str_replace(".", "_", LIBRARY_VERSION)));
	
    $pharFileName = $formattedLibraryName . "__v" . $formattedVersion . ".phar";
	
	$pharFilePath = dirname(__FILE__) . "/output/" . $pharFileName;
	
	if(!is_dir(dirname(__FILE__) . "/output")){
		mkdir(dirname(__FILE__) . "/output");
	}
	
    // clean up
    if (file_exists($pharFilePath)) 
    {
        unlink($pharFilePath);
    }

    if (file_exists($pharFilePath . '.gz')) 
    {
        unlink($pharFilePath . '.gz');
    }

    // create phar
    $phar = new Phar($pharFilePath);

    // start buffering. Mandatory to modify stub to add shebang
    $phar->startBuffering();

    // Create the default stub from main.php entrypoint
    $defaultStub = $phar->createDefaultStub("xml_parser.php");

    // Add the rest of the apps files
    $phar->buildFromDirectory("./parser");

    // Customize the stub to add the shebang
    //$stub = "#!/usr/bin/env php \n" . $defaultStub;
    $stub = "#!/bin/bash php " . PHP_EOL . $defaultStub;

    // Add the stub
    $phar->setStub($stub);

    $phar->stopBuffering();

    // plus - compressing it into gzip  
    $phar->compressFiles(Phar::GZ);

    # Make the file executable
    chmod($pharFilePath, 0770);

    echo "[ $pharFilePath ] successfully created!" . PHP_EOL;
}
catch (Exception $e)
{
	echo "Error in library build: " . $e->getMessage() . PHP_EOL;
}

?>