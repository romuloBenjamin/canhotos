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
/*GET IDENTIFY & READS -> TESSERACT READS*/
//echo json_encode($barCode);
if (is_null($barCode)) {
    /*GET READS*/
    $process->swit = "tesseract-identify-reads";
    $builds = $process->compound_scanner();
    $process->build = $builds;
    $process->swit = "tesseract-identify-steps";
    $builds = $process->compound_scanner();
    echo json_encode($builds);
    //$process->build = $builds;
}
/*GET IDENTIFY & READS -> ZBAR*/
if (!is_null($barCode)) {
    /*GET READS*/
    $process->swit = "zbar-identify-reads";
    $process->build["zbar_read"] = $barCode;
    $builds = $process->compound_scanner();
    $process->build = $builds;
    $process->swit = "zbar-identify";
    $builds = $process->compound_scanner();
}
//$process->build = $builds;
//echo json_encode($process->build);
