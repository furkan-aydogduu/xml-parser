# XML Parser and Validator
## Parser and validator library for documents in XML format. <br>
This parser takes an xml document as input in string format and validates it against [XML standard](https://www.w3.org/TR/xml/). It creates and returns the related document tree if the document passes the validation rules.

#### $\large{\textbf{\textcolor{orange}{Note:}}}$ The validation compliance with the [XML standard](https://www.w3.org/TR/xml/) is incomplete for now. These are the supported validation rules:
- [XML PI Target Definition](https://www.w3.org/TR/xml/#NT-PITarget)
- [Element Definitions](https://www.w3.org/TR/xml/#NT-element)
- [Attribute Definitions](https://www.w3.org/TR/xml/#NT-AttDef)
- [CDATA Sections](https://www.w3.org/TR/xml/#NT-CDSect)
- [Comment Sections](https://www.w3.org/TR/xml/#sec-comments)

#### Library Usage:
- Download the <b>xmlparser__[version].phar</b>  from the repository.
- Place the downloaded <b>phar</b> file in your project structure.
- Use the following code to use the library in your project:
    ```php
    require_once dirname(__FILE__) . "/xmlparser__[version].phar";  //change the directory definition of the library in the require_once command for your project requirements
    
    use org\xmlparser\parser\XMLParser;
    
    $xmlParser = new XMLParser($xmlInputAsString);  //$xmlInputAsString must be the xml document in string format that is to be validated
    
    $xmlDocument = $xmlParser -> parseXMLAndConvertToDocument();
    
    if($xmlDocument !== null){
        echo "XML Document is valid!";
    }
    else{
        echo "Error in XML Document!";
    }
    ```
#### Build Instruction:
There is only windows build support for now.
##### For Windows Builds:
- In the cli, go to the directory in the source project where the builder_win.php exists.
- Run the following command to get a new <b>phar</b> build from the library source project:
  ```php
      php -f builder_win.php
  ```
- The output release of the library will be placed in the output folder of the source project. <br>
Note: The builder script generates the new file with the library name and version information taken from the LIBRARY_NAME and LIBRARY_VERSION definitions that are declared in the [XML Parser](/org/xmlparser/parser/xml_parser.php)  class.

#### Run Instruction:
You can use the following instructions to test the source project directly with the input of test xml files after you make changes on the project: 
- In the cli, go to the <code>[project folder]/org/xmlparser/</code>  directory of the source project
- Run the following command:
  ```php
      php -f run.php test_input5.html
  ```
Note: The input test file (e.g. test_input5.html) must be placed in the <code>[project folder]/org/xmlparser/test/test-cases/</code>  folder to make it available for testing purposes.

#### Testing the project:
You can use the <code>[project folder]/org/xmlparser/test/test_run.php</code> script for alternative testing purposes. You can use the following command from the <code>[project folder]/org/xmlparser/</code> directory to use this script:
 ```php
      php -f test/test_run.php test_input5.html
  ```
Note: The input test file (e.g. test_input5.html) must also be placed in the <code>[project folder]/org/xmlparser/test/test-cases/</code> folder to make it available for testing purposes.
