<?php
class Pagebuilder
{
    var $file;
    var $module;
    var $folder;
    var $build;

    var $extension;
    var $attributes;

    public function __construct()
    {
        (isset($this->extension)) ? $this->extension = true : $this->extension = false;
        $this->build = array();
    }

    /* -------------------->HTML SECTIONS<-------------------- */
    public function loudHTML()
    {
        $htmlPath = DIR_PATH . "cnt-html/" . $this->file . ".html";
        $html = file_get_contents($htmlPath);
        return $html;
    }
    public function createHTMLHEADER()
    {
        $remove = array("[META]", "[TITLE]", "[LINKS]", "[SCRIPTS]");

        /*BASE HTML -> to header*/
        $htmls = $this->build["html"]["header"];
        $header = $htmls["base"];
        /*BUILD HTML*/
        $header = str_replace($remove[0], $htmls["meta"], $header);
        $header = str_replace($remove[1], $htmls["title"], $header);
        $header = str_replace($remove[2], $htmls["links"], $header);
        $header = str_replace($remove[3], $htmls["scripts"], $header);
        echo $header;
    }
    public function loudPAGEParts()
    {
        $loudTemplate = new Pagebuilder();
        $loudTemplate->module = $this->module;
        $loudTemplate->folder = $this->folder;
        $loudTemplate->file = $this->file;
        if ($this->extension == true) return $loudTemplate->loudTemplateHTML_parts();
        if ($this->extension == false) return $loudTemplate->loudTemplatePHP_parts();
    }
    /* -------------------->HTML SECTIONS<-------------------- */
    public function loudDefaultsTemplateHTML()
    {
        var_dump("not implemented");
    }
    public function loudTemplateHTML_parts()
    {
        if ($this->folder == "template/buttons") $htmlPath = DIR_PATH . "cnt-modules/" . trim($this->module) . "-module/" . trim($this->folder) . "/buttons-" . trim($this->file) . "-template.html";
        if ($this->folder == "template/view") $htmlPath = DIR_PATH . "cnt-modules/" . trim($this->module) . "-module/" . trim($this->folder) . "/view-" . trim($this->file) . "-template.html";
        if ($this->folder == "template/tables") $htmlPath = DIR_PATH . "cnt-modules/" . trim($this->module) . "-module/" . trim($this->folder) . "/table-" . trim($this->file) . "-template.html";
        return $htmlPath;
    }
    /* -------------------->PHP SECTIONS<-------------------- */
    public function loudBody()
    {
        $phpPATH = DIR_PATH . "cnt-html/" . $this->file . ".php";
        return $phpPATH;
    }
    public function loudDefaultsTemplatePHP()
    {
        $nArray = array();
        $patterns = $this->build["listas"];
        for ($i = 0; $i < count($patterns->defaultPages); $i++) {
            if ($patterns->defaultPages[$i]->file == $this->file) $nArray[] = $patterns->defaultPages[$i]->file;
        }
        $template = DIR_PATH . "cnt-modules/" . trim($patterns->module) . "-module/" . trim($patterns->folder) . "/template-" . trim($nArray[0]) . "-pages.php";
        /*PLACE TEMPLATE DEFAULTS*/
        $buildDefaults = new Pagebuilder();
        $buildDefaults->build["path"] = $template;
        return $buildDefaults->placer_defaults();
    }
    public function loudTemplatePHP_parts()
    {
        if ($this->folder == "template/forms") $phpPATH = DIR_PATH . "cnt-modules/" . trim($this->module) . "-module/" . trim($this->folder) . "/formulario-" . trim($this->file) . "-template.php";
        if ($this->folder == "template/view") $phpPATH = DIR_PATH . "cnt-modules/" . trim($this->module) . "-module/" . trim($this->folder) . "/view-" . trim($this->file) . "-template.php";
        if ($this->folder == "template/tables") $phpPATH = DIR_PATH . "cnt-modules/" . trim($this->module) . "-module/" . trim($this->folder) . "/table-" . trim($this->file) . "-template.php";
        return $phpPATH;
    }
    /* -------------------->PHP DEFAULTS<-------------------- */
    public function gerador_defaults()
    {
        //var_dump($this);
        /*CARREGA LISTAS DEFAULTS DISPONIVEIS*/
        $loudJSONS = new TryVars();
        $loudJSONS->module = $this->module;
        $loudJSONS->folder = "jsons";
        $loudJSONS->file = "defaults";
        $listas = $loudJSONS->loudJson();
        $this->build["listas"] = $listas;
        /*LOUD TEMPLATE*/
        $defaultys = new Pagebuilder();
        $defaultys->file = $this->file;
        $defaultys->build = $this->build;
        /*RETURN DEFAULT PHP*/
        if ($defaultys->extension == false) $template = $defaultys->loudDefaultsTemplatePHP();
        /*RETURN DEFAULT HTML*/
        if ($defaultys->extension == true) $template = $defaultys->loudDefaultsTemplateHTML();
        return $template;
    }
    public function placer_defaults()
    {
        include $this->build["path"];
    }
}
