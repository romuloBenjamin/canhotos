<?php session_start();
$core = "core";
require_once "../../../cnt-php/defines.php";
require DIR_PATH . "cnt-php/TryVars.php";
///*CALL CONECTIONS*/
$loudConns = new TryVars();
$loudConns->module = "loud";
$loudConns->folder = "jsons";
$loudConns->file = "loudConexoes";
$loudConns->loudConections();
///*CALL MODULES*/
$loudConns->file = "loudMods";
$loudConns->loudModules();
/*LOGIN DATA*/
$pesquisar = new Pesquisar_compound();
$pesquisar->entry = $_POST["entry"];
$pesquisar->swit = $_POST["swit"];
echo $pesquisar->compound_pesquisar_antigos();
