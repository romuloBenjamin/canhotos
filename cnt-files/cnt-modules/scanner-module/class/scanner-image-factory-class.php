<?php
class Scanner_image_factory
{
    var $image;
    var $build;
    var $where;
    var $from;
    var $to;
    var $reader;

    public function __construct()
    {
        $this->build = array();
        $this->build["formatos"] = IMAGES_FORMATS;
        $this->build["sizes"] = array("width" => 0, "height" => 0);
        $this->build["crop"] = array(0, 0, 0, 0);
        $this->build["crop_index"] = 1;
        $this->build["gamma_index"] = 0.200;
        $this->build["blur_indexes"] = array(1, 1.5);
        $this->build["specials"] = array(
            "active" => false,
            "greyScale" => false,
            "enhance" => false,
            "equalize" => false,
            "depth" => false,
            "depth_data" => 1,
            "deskew" => false,
            "deskew_data" => .2,
            "sharpen" => false,
            "sharpen_data" => array(1, 1.1)
        );
        /*ADD STD CLASS*/
        $this->reader = new stdClass();
        $this->identify = new stdClass();
    }

    /*MERGE BUILDS*/
    public function compound_build()
    {
        $builds = new Scanner_image_factory();
        $builds->build = array_merge($builds->build, $this->build);
        return $builds->build;
    }

    /*GET IMAGE PATH*/
    public function get_image_path()
    {
        $this->build["path"]["image"] = array();
        $this->build["path"]["image"][$this->from] = $this->build["path"]["rede"][$this->from];
        $this->build["path"]["image"][$this->to] = $this->build["path"]["rede"][$this->to];
        return;
    }

    /*SET FROM & TO*/
    public function set_from_to()
    {
        switch ($this->where) {
            case 'usuario':
                $this->from = $this->where;
                $this->to = "process";
                break;
            case 'process':
                $this->from = $this->where;
                $this->to = "results";
                break;
                /*default:break;*/
        }
        return;
    }

    /*GET IMAGE SIZES*/
    public function image_samples_sizes()
    {
        $nArray = array();
        /*IMAGICK*/
        $imagick = new Imagick();
        if ($this->where == "usuario") $read_image = $this->build["path"]["image"][$this->from] . "\\" . $this->image;
        if ($this->where != "usuario") $read_image = $this->build["path"]["image"][$this->from] . "\\" . $this->build["images"]->from;
        /*IMAGICK*/
        if (file_exists($read_image)) {
            $imagick->readImage($read_image);
            $nArray["width"] = $imagick->getImageWidth();
            $nArray["height"] = $imagick->getImageHeight();
            /*update sizes*/
            $this->build["sizes"] = $nArray;
        }
        return;
    }

    /*FILE JPEG*/
    public function file_jpeg()
    {
        $xplode = explode(".", $this->image);
        $this->build["images"] = new stdClass();
        ($this->where == "usuario") ? $this->build["images"]->from = $this->image : $this->build["images"]->from = $xplode[0] . ".jpeg";
        $this->build["images"]->to = $xplode[0] . ".jpeg";
        return;
    }

    /*CREATE CROP DATA TO ZBAR*/
    public function create_crop_dimensions()
    {
        $nArray = array();
        /*TAMANHOS E POSIÇÔES PARA CRIACAO DO SAMPLE*/
        switch ($this->build["crop_index"]) {
            case 1:
                $nArray["width"] = 25;
                $nArray["height"] = 30;
                $nArray["x"] = 120;
                $nArray["y"] = 0;
                break;
            case 2:
                $nArray["width"] = 42;
                $nArray["height"] = 40;
                $nArray["x"] = 730;
                $nArray["y"] = 0;
                break;
            case 3:
                $nArray["width"] = 20;
                $nArray["height"] = 30;
                $nArray["x"] = $this->build["sizes"]["width"] - 650;
                $nArray["y"] = 40;
                break;
            case 4:
                $nArray["width"] = 42;
                $nArray["height"] = 20;
                $nArray["x"] = 730;
                $nArray["y"] = 100;
                break;
                /*default:break;*/
        }

        /*DIMENSIONS*/
        #1 -> ZBAR
        #2 -> TEXT PRINCIPAL TESSA
        #3 -> Nº NFE
        #4 -> TEXT PRINCIPAL 2tnt

        /*CALCULAR EM PIXEL -> samples w VS h*/
        $nArray["width"] = ($this->build["sizes"]["width"] * $nArray["width"]) / 100;
        $nArray["height"] = ($this->build["sizes"]["height"] * $nArray["height"]) / 100;
        $this->build["crop"] = $nArray;
        return;
    }

    /*CREATE SAMPLES -> folder process*/
    public function create_sample_inprocess2()
    {
        $this->set_from_to();
        $this->get_image_path();
        $this->image_samples_sizes();
        $this->force_image_horizontal();
        $this->file_jpeg();
        $this->build_image_sample();
        return $this;
    }

    /*CREATE SAMPLE -> folder results*/
    public function create_sample_inresults2()
    {
        $builds = $this->compound_build();
        $this->build = $builds;
        $this->set_from_to();
        $this->get_image_path();
        $this->file_jpeg();
        $this->image_samples_sizes();
        $this->create_crop_dimensions();
        $this->build_image_sample();
        return $this;
    }
    /*FORCE IMAGE ORIENTATIONS HORIZONTAL*/
    public function force_image_horizontal()
    {
        if ($this->build["sizes"]["width"] < $this->build["sizes"]["height"]) {
            $this->image_rotate();
            $this->image_samples_sizes();
            return;
        }
        return;
    }

    /*CREATE IMAGE SAMPLE*/
    public function build_image_sample()
    {
        /*CREATE SAMPLE*/
        $read_image = $this->build["path"]["image"][$this->from] . "\\" . $this->build["images"]->from;
        $imagick = new Imagick();
        if (file_exists($read_image)) {
            $imagick->readImage($read_image);
            $imagick->setResolution(1000, 500);
            $imagick->setImageFormat("jpeg");
            $imagick->despeckleImage();
            /*PLACE IMAGE IN OLDER*/
            if (isset($this->build["crop"])) {
                $imagick->despeckleImage();
                /*SPECIALS*/
                if ($this->build["specials"] == true) {
                    if ($this->build["specials"]["greyScale"] == true) $imagick->setImageType(Imagick::IMGTYPE_GRAYSCALEMATTE);
                    if ($this->build["specials"]["enhance"] == true) $imagick->enhanceImage();
                    if ($this->build["specials"]["equalize"] == true) $imagick->equalizeImage();
                    if ($this->build["specials"]["depth"] == true) $imagick->setImageDepth($this->build["specials"]["depth_data"]);
                    if ($this->build["specials"]["sharpen"] == true) {
                        $sharpen_data = $this->build["specials"]["sharpen_data"];
                        $imagick->sharpenImage($sharpen_data[0], $sharpen_data[1]);
                    }
                }
                $crop_image = $this->build["crop"];
                $imagick->blurImage($this->build["blur_indexes"][0], $this->build["blur_indexes"][1]);
                $imagick->gammaImage($this->build["gamma_index"], Imagick::CHANNEL_ALL);
                $imagick->cropImage($crop_image["width"], $crop_image["height"], $crop_image["x"], $crop_image["y"]);
            }
            /*put image inrede -> process & results*/
            $put_image_rede = $this->build["path"]["image"][$this->to] . "\\" . $this->build["images"]->to;
            (file_exists($put_image_rede)) ? file_put_contents($put_image_rede, "") : "";
            file_put_contents($put_image_rede, $imagick);
            /*put image inlocal -> process & results*/
            $put_image_local = $this->build["path"]["local"][$this->to] . "\\" . $this->build["images"]->to;
            (file_exists($put_image_local)) ? file_put_contents($put_image_local, "") : "";
            file_put_contents($put_image_local, $imagick);
        }
        $imagick->clear();
        $imagick->destroy();
        return;
    }

    /*ROTATE IMAGE*/
    public function image_rotate($deg = 90)
    {
        /*IMAGICK*/
        $imagick = new Imagick();
        if ($this->where == "usuario") $read_image = $this->build["path"]["image"][$this->from] . "\\" . $this->image;
        if ($this->where != "usuario") $read_image = $this->build["path"]["image"][$this->from] . "\\" . $this->build["images"]->from;
        $imagick->readImage($read_image);
        $imagick->rotateImage("#fff", $deg);
        /*ROTATE CONTENTs*/
        (file_exists($read_image)) ? file_put_contents($read_image, "") : "";
        file_put_contents($read_image, $imagick);
        $imagick->clear();
        $imagick->destroy();
        return;
    }

    /*PREPARE READER ZBAR*/
    public function prepare_reader_zbar($try = 0)
    {
        $builds = $this->compound_build();
        $this->build = $builds;
        $this->set_from_to();
        $this->get_image_path();
        $this->file_jpeg();
        $this->image_samples_sizes();
        $this->create_crop_dimensions();
        $read = $this->read_zbar();
        $this->reader->zbar = $read;
        /*IDENTIFICADO PELO ZBAR*/
        if ($this->reader->zbar->code == 200) {
            $this->identify_format("ZBAR");
            $this->get_certificado_data();
            $this->certificar_canhoto();
        } else {
            $this->image_rotate(180);
            $this->build_image_sample();
            if ($try == 0) {
                $try++;
                return $this->prepare_reader_zbar($try);
            }
        }
        return $this->identify;
    }
    public function save_tesseract_complemento_patterns()
    {
        $patterns = new Scanner_patterns();
        $patterns->build = $this->build;
        return $patterns->update_build_path();
    }
    /*SAVE TESSERACT*/
    public function save_tesseract_loop()
    {
        $builds = $this->compound_build();
        $this->build = $builds;
        $this->where = "process";
        $this->set_from_to();
        /*COMPLETAR BUILD*/
        $this->build["scanner"] = $this->image["scanner"];
        $this->build["user"] = $this->image["username"];
        $updateBuild = $this->save_tesseract_complemento_patterns();
        $this->build = $updateBuild;
        /*IDENTIFY*/
        $this->identify->origin = "TESSERACT";
        $this->identify->nfe = $this->image["nfe"];
        $this->identify->cnpj = $this->image["cnpj"];
        $this->identify->raw = $this->image["cnpj"] . $this->image["nfe"];
        /*READER*/
        $this->reader->origin = "TESSERACT";
        $this->reader->nfe = $this->image["nfe"];
        $this->reader->cnpj = $this->image["cnpj"];
        $this->reader->raw = $this->image["cnpj"] . $this->image["nfe"];
        /*CERTIFICAR*/
        $this->get_certificado_data();
        $this->image = $this->image["image"];
        $this->file_jpeg();
        $this->certificar_canhoto();
        //$this->visualizar();
        return;
    }
    //public function visualizar(){ echo json_encode($this); echo "\n"; return; }
    /*PREPARE READER TESSERACT*/
    public function prepare_reader_tesseract()
    {
        $builds = $this->compound_build();
        $this->build = $builds;
        $this->set_from_to();
        $this->get_image_path();
        $this->file_jpeg();
        $this->image_samples_sizes();
        $this->build["crop_index"] = "2";
        $this->create_crop_dimensions();
        $this->build_image_sample();
        $read = $this->read_tessa();
        $this->set_tesseract_reader($read);
        $this->tesseract_identify_empresa();
        $this->tessercat_indetify_nfe();
        return $this->identify;
    }
    /*SET TESSERACT READER*/
    public function set_tesseract_reader($nArray)
    {
        /*SET RETORNO TESSERACT*/
        (!isset($this->reader->tesseract_reader)) ? $this->reader->tesseract_reader = array() : "";
        $this->reader->tesseract_reader[] = $nArray["data"];
        /*SET TESSERACT ARRAY*/
        (!isset($this->reader->exploder)) ? $this->reader->exploder = array() : "";
        $this->reader->exploder = $this->tesseract_exploder($nArray);
        return;
    }
    /*TESSERACT EXPLODER*/
    public function tesseract_exploder($nArray)
    {
        $xplode = explode(" ", $nArray["data"]);
        $tess_xploder = array();
        for ($i = 0; $i < count($xplode); $i++) ($xplode[$i] == " ") ? "" : $tess_xploder[] = $xplode[$i];
        return $tess_xploder;
    }
    /*TESSERACT TRY IDENTIFY EMPRESA*/
    public function tesseract_identify_empresa($try = 0)
    {
        $this->identify->origin = "TESSERACT";
        if (array_intersect(SALES_PRIMARY_KNOW_NAMES, $this->reader->exploder)) {
            $this->identify->cnpj = $this->find_tesseract_company_sales();
        } else if (array_intersect(SANDALO_PRIMARY_KNOW_NAMES, $this->reader->exploder)) {
            $this->identify->cnpj = $this->find_tesseract_company_sandalo();
        } else if (array_intersect(DONA_PRIMARY_KNOW_NAMES, $this->reader->exploder)) {
            $this->identify->cnpj = DONA_DESCARTAVEIS["CNPJ"];
        } else {
            /*VARIAVEIS*/
            $index = 0.100;
            /*GAMA TRY*/
            if ($try < 10) $this->tesseract_gama($index);
            /*ENHANCE TRY*/
            if ($try > 10 && $try < 20) $this->tesseract_gama_enhance($index);

            $try++;
            $this->build_image_sample();
            $read = $this->read_tessa();
            $this->set_tesseract_reader($read);

            /*RETURN*/
            if ($try < 21) return $this->tesseract_identify_empresa($try);
            $this->identify->cnpj = 0;
            return;
        }
        return;
    }
    public function tesseract_gama($index, $type = "add")
    {
        if ($type == "add") return $this->build["gamma_index"] = floatval($this->build["gamma_index"]) + floatval($index);
        if ($type == "remove") return $this->build["gamma_index"] = floatval($this->build["gamma_index"]) - floatval($index);
    }
    public function tesseract_gama_enhance($index)
    {
        $this->build["specials"]["active"] = true;
        $this->build["specials"]["enhance"] = true;
        $this->tesseract_gama($index, "remove");
        return;
    }
    /*TESSERACT FIND COMPANY*/
    public function find_tesseract_company_sales()
    {
        if (array_intersect(SALES_EQUIP["SECONDARY_KNOW_NAMES"], $this->reader->exploder)) {
            return SALES_EQUIP["CNPJ"];
        } else if (array_intersect(SALES_IND["SECONDARY_KNOW_NAMES"], $this->reader->exploder)) {
            return SALES_IND["CNPJ"];
        } else {
            return;
        }
    }
    /*TESSERACT FIND COMPANY*/
    public function find_tesseract_company_sandalo()
    {
        if (array_intersect(SANDALO_EQUIP["SECONDARY_KNOW_NAMES"], $this->reader->exploder)) {
            return SANDALO_EQUIP["CNPJ"];
        } else if (array_intersect(COMERCIAL_SANDALO["SECONDARY_KNOW_NAMES"], $this->reader->exploder)) {
            return COMERCIAL_SANDALO["CNPJ"];
        } else {
            return;
        }
    }
    /*TESSERCT IDENTIFY NFE*/
    public function tessercat_indetify_nfe($try = 0)
    {
        $this->build["crop_index"] = 3;
        $this->create_crop_dimensions();
        $this->build_image_sample();
        $read = $this->read_tessa();
        $read["data"] = mb_strtolower($read["data"], "UTF-8");
        $read["data"] = str_replace(ONLY_NUMBERS, "", $read["data"]);
        $read["data"] = str_replace(REMOVE_SPECIALS, "", $read["data"]);
        $this->set_tesseract_reader($read);
        if (strlen($read["data"]) < 5) {
            /*VARIAVEIS*/
            $index = 0.100;
            $try++;
            if ($try < 5) {
                $this->tesseract_gama($index);
                return $this->tessercat_indetify_nfe($try);
            } else {
                $this->identify->nfe = $read["data"];
            }
        }
        $this->identify->nfe = $read["data"];
        return;
    }
    /*READ TESSERACT*/
    public function read_tessa()
    {
        $tesseract = new Scanner_tesseract();
        $tesseract->build = $this->build;
        $tesseract->entry = $this->image;
        return $tesseract->tesseract_ocr();
    }
    /*READ ZBAR*/
    public function read_zbar()
    {
        $factory = new Scanner_factory();
        $factory->image = $this->image;
        $factory->build = $this->build;
        $factory->build["where"] = $this->where;
        return $factory->zbar_reader();
    }
    /*PEGAR DADOS DO CERTIFICADO*/
    public function get_certificado_data()
    {
        $certificado_path = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]) . "/cert";
        $certificado_file = $this->identify->cnpj . ".pfx";
        if (file_exists($certificado_path . "/" . $certificado_file)) {
            $certificado = file_get_contents($certificado_path . "/" . $certificado_file);
            openssl_pkcs12_read($certificado, $cert, base64_decode(CERTIFICADOS));
            $sign_cert = $cert["cert"];
            $sign_pkey = $cert["pkey"];
            $certificado_file_crt = str_replace(".pfx", ".crt", $certificado_file);
            (!file_exists($certificado_path . "/" . $certificado_file_crt)) ? file_put_contents($certificado_path . "/" . $certificado_file_crt, "") : "";
            file_put_contents($certificado_path . "/" . $certificado_file_crt, $sign_cert . $sign_pkey);
            $CertPriv = openssl_x509_parse(openssl_x509_read($sign_cert));
            $this->build["crt"] = $CertPriv;
            return;
        }
        return;
    }
    /*CERTIFICAR CANHOTO*/
    public function certificar_canhoto()
    {
        $this->create_pdf_file();
        $this->remove_files();
        ($this->identify->origin == "TESSERACT") ? $this->wip_directories() : "";
        return;
    }
    /*REMOVE FILES*/
    public function remove_files()
    {
        $removes = new Scanner_factory();
        $removes->image = $this->image;
        $removes->build = $this->build;
        $removes->reader = $this->reader;
        $removes->entry = $this->identify;
        $removes->prepare_remove_files();
        return;
    }
    /*WIPE DIRECTORIES*/
    public function wip_directories()
    {
        $removes = new Scanner_factory();
        $removes->image = $this->image;
        $removes->build = $this->build;
        $removes->reader = $this->reader;
        $removes->entry = $this->identify;
        $removes->prepare_wip_directories();
        return;
    }
    /*CREATE PDF*/
    public function create_pdf_file()
    {
        $pdf = new Scanner_factory();
        $pdf->image = $this->image;
        $pdf->build = $this->build;
        $pdf->reader = $this->reader;
        $pdf->entry = $this->identify;
        $pdf->create_pdf();
        return;
    }
    /*BUILD FORMATO PARA IDENTIFY*/
    public function identify_format($origin)
    {
        $this->identify->origin = $origin;
        ($origin == "ZBAR") ? $this->identify->raw = $this->reader->zbar->text : "";
        ($origin == "ZBAR") ? $this->identify->cnpj = substr($this->reader->zbar->text, 0, 14) : "";
        ($origin == "ZBAR") ? $this->identify->nfe = substr($this->reader->zbar->text, 14, strlen($this->reader->zbar->text)) : "";
        return;
    }
}
