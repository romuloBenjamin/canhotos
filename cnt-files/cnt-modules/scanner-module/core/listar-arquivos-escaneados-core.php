<?php $core = "core";
require_once "../../../cnt-php/config.php";
require DIR_PATH . "cnt-php/tryVars.php";
/*LOUDS CONEXAO*/
$louds = new TryVars();
$louds->folder = "jsons";
$louds->module = "loud";
$louds->file = "loudConexoes";
$louds->loudConections();
/*LOUD MODULES*/
$louds->folder = "jsons";
$louds->module = "loud";
$louds->file = "loudMods";
$louds->loudModules();
/*VARIAVEIS*/
$nArray = array();
$data = json_decode($_GET["id"]);
/*GERAR PROCESSO*/
$scanner = new Scanner_compound();
$scanner->swit = "initialize-scann";
$scanner->build["scannerID"] = $data->scanner;
$scanner->build["who"] = $data->user;
$images_scanner = $scanner->compound_scanner();
echo json_encode($images_scanner);
