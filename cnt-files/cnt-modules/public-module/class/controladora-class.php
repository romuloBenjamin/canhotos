<?php
class Controladora
{
    var $page;
    var $module;
    var $build;

    public function __construct()
    {
        /*SET TYPE BUILD*/
        $this->build = array();
        /*LOUD CURRENT PAGE*/
        $setPage = new TryVars();
        $slugs = $setPage->get_slug();
        if (empty($slugs)) $this->build["pageName"] = "canhotos";
        if (!empty($slugs)) $this->build["pageName"] = $setPage->get_slug();
        /*LOUD COMUNS PAGE*/
        $loudComuns = new TryVars();
        $loudComuns->module = "public";
        $loudComuns->folder = "jsons";
        $loudComuns->file = "comunPages";
        $pages = $loudComuns->loudJson();
        $this->build["comuns"] = $pages;
    }
    /*PAGE CONTROLLER*/
    public function controller_page()
    {
        $nArray = array();
        $page = new Controladora();
        /*LOUD COMUN PAGES*/
        $comuns = $this->build["comuns"]->comunPages;
        for ($i = 0; $i < count($comuns); $i++) {
            if ($comuns[$i]->file === $this->build["pageName"]) {
                $nArray[] = DIR_PATH . "cnt-modules/" . trim($comuns[$i]->module) . "-module/page-" . trim($comuns[$i]->file) . "-intranet.php";
            }
        }
        /*IF EMPTY OR NULL*/
        if (empty($nArray)) $nArray[] = DIR_PATH . "cnt-modules/public-module/page-canhotos-intranet.php";
        if (is_null($nArray)) $nArray[] = DIR_PATH . "cnt-modules/public-module/page-canhotos-intranet.php";
        $page->page = $nArray[0];
        return $page->loud_controller();
    }
    /*LOUD CONTROLLER*/
    public function loud_controller()
    {
        return $this->page;
    }
}
