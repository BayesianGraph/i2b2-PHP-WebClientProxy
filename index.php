<?php
//Make sure that this is a POST request.
header('Content-type: application/xml');
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    //If it isn't, send back a 405 Method Not Allowed header.
    header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
    exit;
}


//Get the raw POST data from PHP's input stream.
//This raw data should contain XML.
$postData = trim(file_get_contents('php://input'));
$rawxml = new SimpleXMLElement($postData);
//Use internal errors for better error handling.
libxml_use_internal_errors(true);

//If the XML could not be parsed properly.
if($rawxml === false) {
    //Send a 400 Bad Request error.
    header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request", true, 400);
    //Print out details about the error and kill the script.
    foreach(libxml_get_errors() as $xmlError) {
        echo $xmlError->message . "\n";
    }
    exit;
}
$rawxml->message_header->proxy->addChild('client_ip');
$rawxml->message_header->proxy->client_ip = $_SERVER['REMOTE_ADDR'];
//echo $rawxml;

$url = $rawxml->message_header->proxy->redirect_url;
$options = array(
        'http' => array(
        'header'  => "Content-type: application/xml\r\n",
        'method'  => 'POST',
        'content' => http_build_query($rawxml),
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
var_dump($result);