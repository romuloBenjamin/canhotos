<?php
class TryVars
{
    var $module;
    var $folder;
    var $file;
    var $build;

    public function __construct()
    {
        $this->build = array();
    }
    /*SLUGS REMOVE*/
    public function strRemoves()
    {
        $nArray = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ".", "-", "_", "=", "<", ">", "'", "/", "\'", ";");
        $nArray[] = "retorno";
        $nArray[] = "nfes";
        $nArray[] = "php";
        $nArray[] = "html";
        $nArray[] = "index";
        return $nArray;
    }
    /*SLUGS*/
    public function get_slug()
    {
        $removes = new TryVars();
        $url = $_SERVER["REQUEST_URI"];
        $url = explode("?", $url);
        $page = str_replace($removes->strRemoves(), "", $url[0]);
        return $page;
    }
    /*LOUDMODS -> GET LISTA*/
    public function loudJson()
    {
        $listas = file_get_contents(DIR_PATH . "cnt-modules/" . trim($this->module) . "-module/" . trim($this->folder) . "/lista-" . trim($this->file) . "-json.json");
        return json_decode($listas);
    }
    /*LOUDMODS -> SET CONECTIONS*/
    public function loudConections()
    {
        $loud = new TryVars();
        $loud->module = $this->module;
        $loud->folder = $this->folder;
        $loud->file = $this->file;
        $path = $loud->loudJson();
        $this->build["listas"] = json_encode($path->conexoes);
        $loud->build = $this->build;
        $loud->loudExec();
    }
    /*LOUDMODS -> SET MODULES*/
    public function loudModules()
    {
        $loud = new TryVars();
        $loud->module = $this->module;
        $loud->folder = $this->folder;
        $loud->file = $this->file;
        $path = $loud->loudJson();
        $this->build["listas"] = json_encode($path->modules);
        $loud->build = $this->build;
        $loud->loudExec();
    }
    /*LOUDMODS -> EXEC LOUDMODS*/
    public function loudExec()
    {
        $pattern = json_decode($this->build["listas"]);
        for ($i = 0; $i < count($pattern); $i++) {
            if (is_bool($pattern[$i]->folder) === true) {
                if ($pattern[$i]->folder === true) $path = DIR_PATH . "cnt-" . trim($pattern[$i]->module) . "/" . trim(LOCAL) . "/" . trim($pattern[$i]->file) . ".php";
                if ($pattern[$i]->folder === false) $path = DIR_PATH . "cnt-" . trim($pattern[$i]->module) . "/" . trim($pattern[$i]->file) . ".php";
                include $path;
            }
            if (is_string($pattern[$i]->folder) === true) {
                $path = DIR_PATH . "cnt-modules/" . trim($pattern[$i]->module) . "-module/" . trim($pattern[$i]->folder) . "/" . trim($pattern[$i]->file) . "-class.php";
                include $path;
            }
        }
    }
    /*LOUDMODS -> SET LISTAS DEFAULTS*/
    public function loudListas()
    {
        $louds = new TryVars();
        $louds->module = $this->module;
        $louds->folder = $this->folder;
        $louds->file = $this->file;
        $path = $louds->loudJson();
        /*CREATE LISTAS*/
        $listas = $path->listas;
        $listar = new Gerador_listagens();
        for ($i = 0; $i < count($listas); $i++) {
            $listar->listas = $listas[$i]->listas;
            $listar->module = $listas[$i]->module;
            $listar->folder = $listas[$i]->folder;
            $listar->compound_listas();
        }
    }
    /*LOAD HEADER*/
    public function loudHeader()
    {
        $setHeader = new Pagebuilder();
        $setHeader->file = "header";
        $this->build["html"]["header"]["base"] = $setHeader->loudHTML();
        $setHeader->file = "meta";
        $this->build["html"]["header"]["meta"] = $setHeader->loudHTML();
        $setHeader->file = "title";
        $this->build["html"]["header"]["title"] = $setHeader->loudHTML();
        $setHeader->file = "links";
        $this->build["html"]["header"]["links"] = $setHeader->loudHTML();
        $setHeader->file = "scripts";
        $this->build["html"]["header"]["scripts"] = $setHeader->loudHTML();
        $setHeader->build = $this->build;
        return $setHeader->createHTMLHEADER();
    }
    /*LOAD BODY*/
    public function loudBody()
    {
        /*GET BODY PATH*/
        $loud_body = new Pagebuilder();
        $loud_body->file = $this->file;
        $this->build["path"] = $loud_body->loudBody();
        /*PLACE BODY*/
        $placer = new TryVars();
        $placer->build = $this->build;
        return $placer->placeBODY();
    }
    /*PLACE BODY*/
    public function placeBODY()
    {
        include $this->build["path"];
    }
}
