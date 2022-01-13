<?php
class Scanner_compound
{
    var $entry;
    var $swit;
    var $build;

    public function __construct()
    {
        $this->build = array();
        $this->build["path_local"] = $_SERVER['DOCUMENT_ROOT'] . "/2.0/canhotos/";
        $this->build["path_rede"] = REPOS;
        $this->build["path_origin"] = "./scanner";
        $this->build["path_process"] = "./scanner/process";
        $this->build["path_results"] = "./scanner/results";
        $this->build["path_save"] = "./scanner";
        $this->build["path_exec"] = "./cnt-files/cnt-assets/";
    }

    /*COMPOUND SCANNER*/
    public function compound_scanner()
    {
        $compound = new Scanner_compound();
        $compound->entry = $this->entry;
        $compound->swit = $this->swit;
        /*SWITCH CASE*/
        switch ($this->swit) {
            case 'initialize-scann':
                $compound->build = $this->build;
                return $compound->compound_factory_images();
                break;
            case 'create-jpeg':
                $builds = array_merge($compound->build, $this->build);
                $compound->build = $builds;
                return $compound->compound_factory_images();
                break;
            case 'jpeg-get-data-parameters':
                $builds = array_merge($compound->build, $this->build);
                $compound->build = $builds;
                return $compound->compound_factory_images();
                break;
            case 'there-is-need-to-flip-sample':
                $compound->build = $this->build;
                return $compound->compound_factory_images();
                break;
            case 'zbar-identify':
                $compound->build = $this->build;
                return $compound->compound_factory_images();
                break;
            case 'tesseract-identify':
                $compound->build = $this->build;
                return $compound->compound_factory_images();
                break;
            case 'verificar-certificados-expirados':
                $compound->build = $this->build;
                return $compound->compound_factory();
                break;
            case 'save-tesseract-files':
                $compound->build = $this->build;
                return $compound->compound_factory_images();
                break;
                /*default:break; */
        }
    }

    /*FACTORY IMAGES*/
    public function compound_factory_images()
    {
        $imagicks = new Scanner_image_factory();
        $imagicks->swit = $this->swit;
        $imagicks->image = $this->entry;
        $imagicks->build = $this->build;
        return $imagicks->images_factory();
    }

    /*FACTORY*/
    public function compound_factory()
    {
        $factory = new Scanner_factory();
        $factory->entry = $this->entry;
        $factory->swit = $this->swit;
        $factory->build = $this->build;
        return $factory->factory_compound();
    }
}
