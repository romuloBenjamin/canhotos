<?php session_start();
$core = "core";
require_once "../../../cnt-php/config.php";
require DIR_PATH . "cnt-php/TryVars.php";
/*CALL CONECTIONS*/
$loudConns = new TryVars();
$loudConns->module = "loud";
$loudConns->folder = "jsons";
$loudConns->file = "loudConexoes";
$loudConns->loudConections();
/*LOUD MODULES*/
$loudConns->file = "loudMods";
$loudConns->loudModules();
/*AUTO LOAD*/
require_once('../../../../vendor/autoload.php');
/*SAVE TESSA*/
$factory = new Scanner_compound();
$factory->swit = "save-tesseract-files";
$_POST = json_decode(file_get_contents('php://input'), true);
$factory->entry = $_POST;
echo $factory->compound_scanner();
