<?php
require_once "../cnt-php/config.php";
require "../cnt-php/tryVars.php";

if ($_GET) {

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

    /*LOUD AUTO LOUD*/
    require_once('../../vendor/autoload.php');
    /*VARIAVEIS*/
    $nArray = array();
    /*GERAR PROCESSO*/
    $scanner = new Scanner_compound();
    $scanner->swit = "initialize-scann";
    $images_scanner = $scanner->compound_scanner();
    for ($i = 0; $i < count($images_scanner); $i++) {
        /*INIT PROCESS*/
        $images = $images_scanner[$i];
        $scanner->entry = $images;
        $scanner->swit = "create-jpeg";
        $new_images = $scanner->compound_scanner();
        $scanner->entry = $new_images;
        $scanner->swit = "jpeg-get-data-parameters";
        $data = $scanner->compound_scanner();
        if (!is_null($data)) $nArray[] = $data;
    }
    var_dump($nArray);
    /*PARA TESSERACT
    if (!empty($nArray)) {
        $buildPage = new Pagebuilder();
        $buildPage->file = "alert-tesseract";
        $buildPage->module = "scanner";
        $buildPage->folder = "template/view";
        $page_path = $buildPage->loudTemplatePHP_parts();
        $buildPage->build["ref"] = $nArray;
        $buildPage->build["path"] = $page_path;
        $buildPage->placer_defaults();
    }*/
} else {
    echo 'fail';
}
