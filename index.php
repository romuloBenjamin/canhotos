<?php session_start();
include "./cnt-files/cnt-php/fakeSessions.php";
include "cnt-files/cnt-php/config.php";
include DIR_PATH . "cnt-php/tryVars.php";
$louds = new TryVars();
$louds->module = "loud";
$louds->folder = "jsons";
//$louds->file = "loudConexoes";
//$louds->loudConections();
/*LOUD MODULES*/
$louds->file = "loudMods";
$louds->loudModules();
/*LOUD LISTAS*/
$louds->file = "loudListas";
$louds->loudListas();
/*HEADER & BODY PAGE*/
echo "<!DOCTYPE html><html lang=\"pt-BR\">";
$loudHTML = new TryVars();
$loudHTML->loudHeader();
$loudHTML->file = "body";
$loudHTML->loudBody();
echo "</html>";
