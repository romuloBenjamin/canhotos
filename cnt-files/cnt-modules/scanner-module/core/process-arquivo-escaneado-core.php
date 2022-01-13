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
/*GERAR PROCESSO -> create init samples*/
$process = new Scanner_compound();
$process->entry = $data->file;
$process->swit = "create-jpeg";
$process->build = array();
$process->build["scannerID"] = $data->scanner;
$process->build["who"] = $data->user;
$new_images = $process->compound_scanner();
/*GERAR PROCESSO -> get size datas*/
$process->entry = $new_images;
$process->swit = "jpeg-get-data-parameters";
$builds = $process->compound_scanner();
$process->build = $builds;
/*CONFIRM IF NEED TO FLIP IMAGE & BAR CODE READER*/
$process->swit = "there-is-need-to-flip-sample";
$barCode = $process->compound_scanner();
/*GET IDENTIFY -> TESSERACT*/
if (is_null($barCode)) {
    $process->swit = "tesseract-identify";
    $builds = $process->compound_scanner();
}
/*GET IDENTIFY -> ZBAR*/
if (!is_null($barCode)) {
    $process->swit = "zbar-identify";
    $process->build["zbar_read"] = $barCode;
    $builds = $process->compound_scanner();
}
echo json_encode($builds);
//if (!is_null($data)) $nArray[] = $data;
//echo json_encode($images_scanner);
