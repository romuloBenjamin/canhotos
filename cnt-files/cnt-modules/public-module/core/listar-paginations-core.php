<?php session_start();
$core = "core";
require_once "../../../cnt-php/defines.php";
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
/*CALL PAGINATIONS*/
$louds = new Paginations();
$louds->swit = $_POST["swit"];
$louds->entry = $_POST["entry"];
echo $louds->paginations_compound();
