<?php
class Scanner_barcode
{
    var $entry;
    var $build;

    public function __construct()
    {
        $this->build = array();
        $this->build["batch"] = "execute-barcode-bat.bat";
    }

    /*MERGE BUILD IN TERSSERACT*/
    public function barcode_merge_build($builds)
    {
        $bar = new Scanner_tesseract();
        return array_merge($bar->build, $builds);
    }

    /*BARCODE SCANNER*/
    public function barcode_scanner()
    {
        $bar = new Scanner_barcode();
        $bar->entry = $this->entry;
        $bar->build = $bar->barcode_merge_build($this->build);
        $commands = $bar->barcode_commands();
        $bar->build["commands"] = $commands;
        //$bar->barcode_build_bats();
        //$bar->tesseract_execute();
        //return $bar->tesseract_mysteries();
    }
    /*BARCODE SCANNER COMMANDS*/
    public function barcode_commands()
    {
        $file_and_extensions = explode(".", $this->entry);
        /*BAT's COMMAND*/
        $commands = array(
            "cd cnt-files\cnt-assets\zbar"
        );
        $commands[] = "zbarimg -d ../images/teste/results/" . $this->entry . " ../images/teste/results/" . $file_and_extensions[0] . "";
        return $commands;
    }
    /*BARCODE SCANNER BAT*/
    public function barcode_build_bats()
    {
        $file_path_bats = $this->build["path_real"] . str_replace("./", "", $this->build["path_exec"]) . $this->build["batch"];
        if (file_exists($file_path_bats)) file_put_contents($file_path_bats, "");
        file_put_contents($file_path_bats, implode("", $this->build["commands"]));
        return;
    }
}
