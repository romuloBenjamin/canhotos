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
//(!isset($data->swit)) ? $data->swit = "initialize" : $data->swit = "undefined";
//echo json_encode($data);

/*GERAR PROCESSO -> create ambient de processamento*/
$process = new Scanner_compound();
$process->entry = json_encode($data);
$process->compound_build();

/*CRIAR AMBIENTE DE PROCESSAMENTOS NO SERVIDOR*/
/*GERA LISTA PARA IDENTIFICAR*/
if ($data->swit == "get-file-list") {
    $process->compound_ambient_patterns();
    $process->compound_build_prepath();
    $process->entry = array("path" => "usuario", "where" => "rede");
    $lista_arquivos = $process->compound_lista_canhotos();
    echo json_encode($lista_arquivos);
}
/*CREATE FILE IN PROCESS*/
if ($data->swit == "get-file-data-process") {
    $process->entry = $data->file;
    $process->compound_file_process2();
    $zbar = $process->reader_zbar();
    if (count(get_object_vars($zbar)) == 0) {
        $tesse = $process->reader_tesseract();
        echo json_encode($tesse);
    } else {
        echo json_encode($zbar);
    }
}
