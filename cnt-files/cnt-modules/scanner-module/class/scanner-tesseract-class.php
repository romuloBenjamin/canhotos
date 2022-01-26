<?php
class Scanner_tesseract
{
    var $entry;
    var $build;

    public function __construct()
    {
        $this->build = array();
        $this->build["batch"] = "execute-tesseract-bat.bat";
    }

    /*MERGE BUILD IN TERSSERACT*/
    public function tesseract_merge_build($builds)
    {
        $ocr = new Scanner_tesseract();
        return array_merge($ocr->build, $builds);
    }

    /*TESSERACT*/
    public function tesseract_ocr()
    {
        $ocr = new Scanner_tesseract();
        $ocr->entry = $this->entry;
        $ocr->build = $ocr->tesseract_merge_build($this->build);
        $commands = $ocr->tesseract_commands();
        $ocr->build["commands"] = $commands;
        $ocr->tesseract_build_bats();
        $ocr->tesseract_execute();
        return $ocr->tesseract_mysteries();
    }
    /*TESSERACT COMMANDS*/
    public function tesseract_commands()
    {
        $file_and_extensions = explode(".", $this->entry);
        /*BAT's COMMAND*/
        $commands = array(
            "cd c:\\",
            "\n",
            "cd " . $this->build["path_local"] . "\n",
            "cd cnt-files\\cnt-assets\n"
        );
        $path_rede = $this->build["path"]["rede"]["results"] . "\\" . $this->build["images"]->to;
        $commands[] = "tesseract " . $path_rede . " " . str_replace(".jpeg", "", $path_rede) . " -l por";
        return $commands;
    }
    /*BUILD TESSERACT BAT*/
    public function tesseract_build_bats()
    {
        $file_path_bats = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]) . $this->build["batch"];
        if (file_exists($file_path_bats)) file_put_contents($file_path_bats, "");
        file_put_contents($file_path_bats, implode("", $this->build["commands"]));
        return;
    }
    /*EXECUTE TESSERACT*/
    public function tesseract_execute()
    {
        $file_path_bats = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]) . $this->build["batch"];
        return exec($file_path_bats);
    }
    /*LER O TESSERACT*/
    public function tesseract_mysteries()
    {
        $file_and_extensions = explode(".", $this->entry);
        $path_results = $this->build["path"]["rede"]["results"];
        $content = file_get_contents($path_results . "\\" . $file_and_extensions[0] . ".txt");
        $content = trim(str_replace(REMOVE_SPECIALS, "", $content));
        if (strlen($content) != 0) {
            return array("status" => "1", "msn" => "ok", "data" => $content);
        } else {
            return array("status" => "0", "msn" => "flip agin", "data" => "");
        }
    }
}
