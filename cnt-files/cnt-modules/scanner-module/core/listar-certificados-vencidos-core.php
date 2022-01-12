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
$factory = new Scanner_compound();
$factory->swit = "verificar-certificados-expirados";
$factory->entry = array("10290557000168.pfx", "21823607000141.pfx", "30379727000192.pfx", "35765246000139.pfx");
echo $factory->compound_scanner();
