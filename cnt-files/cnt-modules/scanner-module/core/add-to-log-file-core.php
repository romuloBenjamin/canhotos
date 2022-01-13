<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$jsonFile = "../jsons/identify-process-log-json.json";
$content;
if (file_exists($jsonFile)) {
    $content = json_decode(file_get_contents($jsonFile), true);
} else {
    $content = array();
}
array_push($content, $_POST);
file_put_contents($jsonFile, json_encode($content));
echo file_get_contents($jsonFile);
