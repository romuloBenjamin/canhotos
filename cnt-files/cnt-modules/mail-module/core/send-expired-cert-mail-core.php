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

/*LOUD VENDOR*/
require "../../../../vendor/autoload.php";
/*LOUD VENDOR*/

$_POST = json_decode(file_get_contents('php://input'), true);
echo json_encode($_POST["certificados"]);

$loudConns->file = "loudMods";
$loudConns->loudModules();
/*CALL MODULE MAILER*/
$mailer = new Mailer_module();
$mailer->entry = json_encode($_POST["certificados"]);
//$mailer->mailer_compound();
