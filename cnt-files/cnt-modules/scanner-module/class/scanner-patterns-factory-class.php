<?php
class Scanner_patterns
{
    var $entry;
    var $build;
    var $swit;
    var $innerBuild;

    public function __construct()
    {
        $this->innerBuild = array();
    }

    /*CREATE AMBIENT DE PROCESSAMENTO usuario*/
    public function patterns_ambient()
    {
        $path_rede_scanner = $this->build["path_rede"] . "scanner\\" . $this->build["scanner"];
        $path_local_scanner = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scanner"];
        if (!is_dir($path_rede_scanner)) mkdir($path_rede_scanner, 0777, true);
        if (!is_dir($path_local_scanner)) mkdir($path_local_scanner, 0777, true);
        return;
    }

    /*CREATE AMBIENT DE PROCESSAMENTO usuario*/
    public function patterns_ambient_usuario()
    {
        $path_rede_usuario = $this->build["path_rede"] . "scanner\\" . $this->build["scanner"] . "\\" . $this->build["user"];
        $path_local_usuario = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scanner"] . "/" . $this->build["user"];
        if (!is_dir($path_rede_usuario)) mkdir($path_rede_usuario, 0777, true);
        if (!is_dir($path_local_usuario)) mkdir($path_local_usuario, 0777, true);
        return;
    }

    /*CREATE AMBIENT DE PROCESSAMENTO -> process*/
    public function patterns_ambient_process()
    {
        $path_rede_scanner_process = $this->build["path_rede"] . "scanner\\" . $this->build["scanner"] . "\\" . $this->build["user"] . "\\process";
        $path_local_scanner_process = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scanner"] . "/" . $this->build["user"] . "/process";
        if (!is_dir($path_rede_scanner_process)) mkdir($path_rede_scanner_process, 0777, true);
        if (!is_dir($path_local_scanner_process)) mkdir($path_local_scanner_process, 0777, true);
        return;
    }
    /*CREATE AMBIENT DE PROCESSAMENTO -> resultados*/
    public function patterns_ambient_results()
    {
        $path_rede_scanner_results = $this->build["path_rede"] . "scanner\\" . $this->build["scanner"] . "\\" . $this->build["user"] . "\\results";
        $path_local_scanner_results = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scanner"] . "/" . $this->build["user"] . "/results";
        if (!is_dir($path_rede_scanner_results)) mkdir($path_rede_scanner_results, 0777, true);
        if (!is_dir($path_local_scanner_results)) mkdir($path_local_scanner_results, 0777, true);
        return;
    }

    /*UPDATE BUILD ADD VAR path*/
    public function update_build_path()
    {
        /*PATH REDE*/
        $this->build["path"] = array();
        $this->build["path"]["rede"] = array();
        $this->build["path"]["rede"]["origin"] = $this->build["path_rede"] . "scanner\\" . $this->build["scanner"];
        $this->build["path"]["rede"]["usuario"] = $this->build["path_rede"] . "scanner\\" . $this->build["scanner"] . "\\" . $this->build["user"];
        $this->build["path"]["rede"]["process"] = $this->build["path_rede"] . "scanner\\" . $this->build["scanner"] . "\\" . $this->build["user"] . "\\process";
        $this->build["path"]["rede"]["results"] = $this->build["path_rede"] . "scanner\\" . $this->build["scanner"] . "\\" . $this->build["user"] . "\\results";
        /*PATH REDE*/
        $this->build["path"]["local"] = array();
        $this->build["path"]["local"]["origin"] = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scanner"];
        $this->build["path"]["local"]["usuario"] = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scanner"] . "/" . $this->build["user"];
        $this->build["path"]["local"]["process"] = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scanner"] . "/" . $this->build["user"] . "/process";
        $this->build["path"]["local"]["results"] = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scanner"] . "/" . $this->build["user"] . "/results";
        return $this->build;
    }
}
