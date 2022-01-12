<?php

$tag = "[Identify-Processes-Running] ";
// Prints an encoded message
function log_message_as_json($message)
{
    echo $GLOBALS["tag"] . json_encode($message);
    echo "\n";
}
function log_message($message = "")
{
    echo $GLOBALS["tag"] . $message . "\n";
}
// The path of the json file
$json_file = "../jsons/lista-identify-processes-running-json.json";

// The received data
$_POST = json_decode(file_get_contents('php://input'), true);
// Turn the data into an array
$data = json_decode(json_encode($_POST), true);

// The user key and key content
$userKey;
$userContent;

log_message_as_json($data);

// Set the values from the data received
foreach ($data as $key => $content) {
    $userKey = $key;
    $userContent = empty($content) ? (object)[] : $content;
    log_message_as_json($key);
}

log_message("Is empty?");
log_message_as_json(empty($_POST));

// If the file doesn't exist, create it with the new data
if (!file_exists($json_file)) {
    $object = (object)[];
    $object->$userKey = $userContent;
    file_put_contents($json_file, json_encode($object));
    log_message_as_json($object);
} else {
    // Add the new data to the current file
    $json = json_decode(file_get_contents($json_file));
    $json->$userKey = $userContent;
    file_put_contents($json_file, json_encode($json));
    log_message_as_json($json);
}
