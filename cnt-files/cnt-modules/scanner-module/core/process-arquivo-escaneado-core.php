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
/*ADD VENDOR*/
require_once('../../../../vendor/autoload.php');
/*VARIAVEIS*/
$data = json_decode($_GET["id"]);
/*GERAR PROCESSO*/
$process = new Scanner_compound();
$process->entry = $data->file;
$process->swit = "create-jpeg";
$process->build = array();
$process->build["scannerID"] = $data->scanner;
$process->build["who"] = $data->user;
$new_images = $process->compound_scanner();
$process->entry = $new_images;
$process->swit = "jpeg-get-data-parameters";
$data = $process->compound_scanner();
//echo json_encode($data);
//if (!is_null($data)) $nArray[] = $data;
//echo json_encode($images_scanner);
