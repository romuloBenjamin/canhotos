<?php

use setasign\Fpdi\PdfParser\Filter\Ascii85;

class Scanner_image_factory
{
    var $image;
    var $swit;
    var $build;

    public function __construct()
    {
        $this->build = array();
        $this->build["formatos"] = IMAGES_FORMATS;
        $this->build["sizes"] = array("width" => 0, "height" => 0);
        $this->build["crop"] = array(0, 0, 0, 0);
        $this->build["crop_index"] = 1;
        $this->build["tesseract_read"] = "";
        $this->build["zbar_read"] = "";
        $this->build["identify"] = array();
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
        $this->build["positions"] = 0;
    }

    /*FACTORY IMAGES*/
    public function images_factory()
    {
        $factory = new Scanner_image_factory();
        $get_factory = new Scanner_factory();
        switch ($this->swit) {
            case 'initialize-scann':
                $builds = $get_factory->factory_mergeBuild($factory->build, $this->build);
                $get_factory->build = $builds;
                return $get_factory->factory_scandir();
                break;
            case 'create-jpeg':
                /*AJUSTAR BUILDS*/
                $builds = $get_factory->factory_mergeBuild($factory->build, $this->build);
                $get_factory->build = $builds;
                $get_factory->image = $this->image;
                $get_factory->factory_identify_sample_init(false, "path_origin");
                $images = explode(".", $this->image);
                return $images[0] . ".jpeg";
                break;
            case 'jpeg-get-data-parameters':
                /*MERGE BUIDS*/
                $builds = $get_factory->factory_mergeBuild($factory->build, $this->build);
                $get_factory->build = $builds;
                $get_factory->image = $this->image;
                /*CONFIRMA W VS H*/
                $image_sizes = $get_factory->factory_getSizes();
                $get_factory->build["sizes"] = $image_sizes;
                /*FACTORY DATA*/
                $factory->image = $this->image;
                $factory->build = $get_factory->build;
                return $factory->images_factory_orientations();
                break;
            case 'there-is-need-to-flip-sample':
                $factory->image = $this->image;
                $factory->build = $this->build;
                return $factory->images_barcode_reader();
                break;
            case 'zbar-identify-reads':
                $factory->image = $this->image;
                $factory->build = $this->build;
                return $factory->images_factory_identify_init();
                break;
            case 'zbar-identify':
                $factory->image = $this->image;
                $factory->build = $this->build;
                $factory->swit = "start-to-certify";
                return $factory->images_factory();
                break;
            case 'tesseract-identify-reads':
                $factory->image = $this->image;
                $factory->build = $this->build;
                return $factory->images_factory_tesseract_init();
                break;
            case 'tesseract-identify-steps':
                /*SOMENTE PARA TESSERACT IDENT*/
                $factory->build = $this->build;
                $factory->image = $this->image;
                $ident = $factory->images_factory_tesseract_steps();
                return $ident;
                break;
            case 'start-to-certify':
                $factory->build = $this->build;
                $factory->image = $this->image;
                if (is_array($factory->build["identify"])) {
                    /*IDENTIFY VIA ZBAR*/
                    if ($factory->build["identify"]["origin"] == "ZBAR") return $factory->images_factory_certify();
                    /*IDENTIFY VIA TESSERACT*/
                    if ($factory->build["identify"]["origin"] == "TESSERACT") {
                        $factory->swit = "confirm-tesseract";
                        $factory->build = $factory->images_factory_certify();
                        return $factory->images_factory();
                    }
                    /*FAIL TO IDENTIFY*/
                    if ($factory->build["identify"]["origin"] == "FAILS-TESSERACT") {
                        $factory->swit = "confirm-tesseract";
                        return $factory->images_factory();
                    }
                }
                break;
            case 'confirm-tesseract':
                $this->swit = "";
                $this->build["crt"] = "";
                $this->build["image"] = $this->image;
                return $this->build;
                break;
            case 'save-tesseract-files':
                $builds = $get_factory->factory_mergeBuild($factory->build, $this->build);
                $factory->build = $builds;
                $factory->image = $this->image;
                return $factory->images_factory_tesseract_save();
                break;
                /*default:break; */
        }
    }
    /*------------------------------------------>ORIENTATIONS<-------------------------------------------*/
    public function images_factory_orientations()
    {
        /*SCANNER IMAGE*/
        $factory = new Scanner_image_factory();
        $factory->build = $this->build;
        $factory->image = $this->image;
        /*SCANNER FACTORY*/
        $get_factory = new Scanner_factory();
        $get_factory->build = $factory->build;
        $get_factory->image = $factory->image;
        /*INIT ORIENTATION*/
        $image_sizes = $get_factory->factory_getSizes();
        if ($image_sizes["width"] > $image_sizes["height"]) {
            $get_factory->build["sizes"] = $image_sizes;
            $factory->build = $get_factory->build;
            return $factory->build;
        } else {
            /*ROTATE IMAGE TO TRY PUT IMAGE IN LANDSCAPE*/
            $get_factory->factory_rotate(90, "path_process", "path_process");
            $get_factory->factory_rotate(90, "path_process", "path_process", "local");
            $image_sizes = $get_factory->factory_getSizes();
            $get_factory->build["sizes"] = $image_sizes;
            $factory->build = $get_factory->build;
            return $factory->images_factory_orientations();
        }
    }
    /*--------------------------------------------->IDENTIFY<---------------------------------------------*/
    public function images_factory_identify_init()
    {
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        $identify->image = $this->image;
        /*SCAN FACTORY*/
        $factory = new Scanner_factory();
        $factory->image = $identify->image;
        $factory->build = $identify->build;
        /*INIT IDENTIFY*/
        $identify->build["identify"] = array();
        /*CONF ZBAR*/
        if (!empty($factory->build["zbar_read"])) {
            $identify->build["identify"]["origin"] = "ZBAR";
            $identify->build["identify"]["cnpj"] = '"' . substr($factory->build["zbar_read"], 0, 14) . '"';
            $identify->build["identify"]["nfe"] = substr($factory->build["zbar_read"], 14, strlen($factory->build["zbar_read"]));
            return $identify->build;
        }
        /*CONF TESSERACT*/
        if (!empty($factory->build["tesseract_read"])) {
            $identify->build["identify"]["origin"] = "TESSERACT";
            $identify->build["identify"]["cnpj"] = "";
            $identify->build["identify"]["nfe"] = "";
            return $identify->build;
        }
    }
    /*------------------------------------------>LEITOR ZBAR<------------------------------------------*/
    public function images_barcode_reader($try = 0)
    {
        $factory = new Scanner_image_factory();
        $factory->build = $this->build;
        $factory->image = $this->image;
        /*SCANNER FACTORY*/
        $get_factory = new Scanner_factory();
        $get_factory->build = $factory->build;
        $get_factory->image = $factory->image;
        /*CROP DATA AND CREATE SAMPLE*/
        $get_factory->build["crop_index"] = 3;
        $dimensions = $get_factory->factory_crop_dimensions();
        $get_factory->build["crop"] = $dimensions;
        $get_factory->factory_identify_sample_init(true);
        /*READ BARCODE*/
        $barCode = $get_factory->factory_read_barcode();
        if ($barCode["code"] == 200) {
            if ($barCode["text"] == "") return;
            if ($barCode["text"] != "") return $barCode["text"];
        } else {
            $try++;
            $builds = $factory->images_factory_orientations();
            $factory->build = $builds;
            if ($try == 3) return;
            if ($try <= 3) return $factory->images_barcode_reader($try);
        }
    }
    /*------------------------------------------->LEITOR TESSERACT<-------------------------------------*/
    public function images_factory_tesseract_init()
    {
        /*SCANNER IMAGE*/
        $factory = new Scanner_image_factory();
        $factory->image = $this->image;
        $factory->build = $this->build;
        $factory->build["crop_index"] = 5;
        /*SCANNER FACTORY*/
        $get_factory = new Scanner_factory();
        $get_factory->build = $factory->build;
        $get_factory->image = $factory->image;
        /*CHANGE CROP PATTERNS*/
        $crops = $get_factory->factory_crop_dimensions();
        $get_factory->build["crop"] = $crops;
        $factory->build = $get_factory->build;
        /*CREATE NEW SAMPLE*/
        $get_factory->factory_identify_sample_init(true);
        /*READ TESSERACT*/
        $tessa = $get_factory->factory_read_tesseract();
        if (intval($tessa["status"]) == 0) {
            /*FLIP 90º ROTATE*/
            $get_factory->factory_rotate(90, "path_process", "path_process");
            $get_factory->factory_rotate(90, "path_process", "path_process", "local");
            /*NEW SIZES*/
            $sizes = $get_factory->factory_getSizes();
            $factory->build["sizes"] = $sizes;
            /*ORIENTATIONS*/
            $builds = $factory->images_factory_orientations();
            $factory->build = $builds;
            return $factory->images_factory_tesseract_init();
        }
        $factory->build["tesseract_read"] = $tessa["data"];
        return $factory->build;
    }
    /*IDENT STEPS*/
    public function images_factory_tesseract_steps($try = 0)
    {
        $factory = new Scanner_image_factory();
        $factory->build = $this->build;
        $factory->image = $this->image;
        /*SCANNER FACTORY*/
        $get_factory = new Scanner_factory();
        $get_factory->build = $factory->build;
        $get_factory->image = $factory->image;
        /*GET TESSERACT READS*/
        if (empty($this->build["tesseract_read"])) $tess_read = array();
        if (is_null($this->build["tesseract_read"])) $tess_read = array();
        if (!empty($this->build["tesseract_read"])) $tess_read = $get_factory->factory_xplode($this->build["tesseract_read"]);
        /*INTERSECT TESSERACT*/
        if (count(array_intersect(SALES_PRIMARY_KNOW_NAMES, $tess_read)) > 0) {
            $builds = $factory->images_factory_identify_init();
            $factory->build = $builds;
            /*GET CNPJ DA EMPRESA*/
            $cnpj = $factory->images_factory_empresa_cnpj();
            $factory->build["identify"]["cnpj"] = $cnpj;
            $nfe = $factory->images_factory_empresa_nfe();
            $factory->build["identify"]["nfe"] = $nfe;
            return $factory->build;
        } else {
            $try++;
            $get_factory->build["gamma_index"] = floatval($get_factory->build["gamma_index"]) + .125;
            $get_factory->build["specials"]["active"] = true;
            $get_factory->build["specials"]["sharpen"] = true;
            $get_factory->build["specials"]["sharpen_data"][0] = 1;
            $get_factory->build["specials"]["sharpen_data"][1] = floatval($get_factory->build["specials"]["sharpen_data"][1]) + .030;
            /*RECREATE SAMPLE AND READ*/
            $get_factory->factory_identify_sample_init(true);
            $reads_raws = $get_factory->factory_read_tesseract();
            /*UPDATE TESSERACT READS*/
            $get_factory->build["tesseract_read"] = $reads_raws["data"];
            /*UPDATE BUILD*/
            $factory->build = $get_factory->build;
            /*FINALIZA*/
            if ($try < 10) return $factory->images_factory_tesseract_steps($try);
            if ($try >= 10) return $factory->build;
        }
    }
    /*GET CNPJ*/
    public function images_factory_empresa_cnpj($try = 0)
    {
        $factory = new Scanner_image_factory();
        $factory->image = $this->image;
        $factory->build = $this->build;
        if ($try == 0) $factory->build["gamma_index"] = 0.200;
        /*SCANNER FACTORY*/
        $get_factory = new Scanner_factory();
        $get_factory->build = $factory->build;
        $get_factory->image = $factory->image;
        /*IDENTIFICAR EMPRESA*/
        if (empty($this->build["tesseract_read"])) $tess_read = array();
        if (is_null($this->build["tesseract_read"])) $tess_read = array();
        if (!empty($this->build["tesseract_read"])) $tess_read = $get_factory->factory_xplode($this->build["tesseract_read"]);
        if (count(array_intersect(SALES_EQUIP["SECONDARY_KNOW_NAMES"], $tess_read)) > 0) {
            return SALES_EQUIP["CNPJ"];
        } else {
            $try++;
            $get_factory->build["gamma_index"] = floatval($get_factory->build["gamma_index"]) + .125;
            $get_factory->build["specials"]["active"] = true;
            $get_factory->build["specials"]["sharpen"] = true;
            $get_factory->build["specials"]["sharpen_data"][0] = 1;
            $get_factory->build["specials"]["sharpen_data"][1] = floatval($get_factory->build["specials"]["sharpen_data"][1]) + .030;
            /*RECREATE SAMPLE AND READ*/
            $get_factory->factory_identify_sample_init(true);
            $reads_raws = $get_factory->factory_read_tesseract();
            /*UPDATE TESSERACT READS*/
            $get_factory->build["tesseract_read"] = $reads_raws["data"];
            /*UPDATE BUILD*/
            $factory->build = $get_factory->build;
            /*FINALIZA*/
            if ($try < 10) return $factory->images_factory_empresa_cnpj($try);
            if ($try >= 10) return "";
        }
    }
    /*GET NFE*/
    public function images_factory_empresa_nfe($try = 0)
    {
        $factory = new Scanner_image_factory();
        $factory->build = $this->build;
        $factory->image = $this->image;
        if ($try == 0) $factory->build["gamma_index"] = 0.200;
        /*SCANNER FACTORY*/
        $get_factory = new Scanner_factory();
        $get_factory->build = $factory->build;
        $get_factory->image = $factory->image;
        /*CREATE CROP & SAMPLE*/
        $get_factory->build["crop_index"] = 4;
        $crop = $get_factory->factory_crop_dimensions();
        $get_factory->build["crop"] = $crop;
        /*CREATE SAMPLE & READ*/
        $get_factory->factory_identify_sample_init(true);
        $reads_nfe = $get_factory->factory_read_tesseract();
        $reads_nfe = str_split(mb_strtolower($reads_nfe["data"], "UTF-8"));
        $reads_nfe = implode("", str_replace(ONLY_NUMBERS, "", $reads_nfe));
        if (strlen($reads_nfe) > 4) {
            return $reads_nfe;
        } else {
            $try++;
            $get_factory->build["gamma_index"] = floatval($get_factory->build["gamma_index"]) + .125;
            $get_factory->build["specials"]["active"] = true;
            $get_factory->build["specials"]["sharpen"] = true;
            $get_factory->build["specials"]["sharpen_data"][0] = 1;
            $get_factory->build["specials"]["sharpen_data"][1] = floatval($get_factory->build["specials"]["sharpen_data"][1]) + .030;
            /*RECREATE SAMPLE AND READ*/
            $get_factory->factory_identify_sample_init(true);
            $reads_raws = $get_factory->factory_read_tesseract();
            /*UPDATE TESSERACT READS*/
            $get_factory->build["tesseract_read"] = $reads_raws["data"];
            /*UPDATE BUILD*/
            $factory->build = $get_factory->build;
            /*FINALIZA*/
            if ($try < 10) return $factory->images_factory_empresa_nfe($try);
            if ($try >= 10) return "";
        }
    }
    /*--------------------------------------------->IDENTIFICAÇÃO DO CANHOTO<---------------------------------------------*/
    /*SET IDENTIFY PARA BARCODE*/
    public function images_factory_identify($barCode)
    {
        $identify = new Scanner_image_factory();
        $identify->image = $this->image;
        $identify->build = $this->build;
        $identify->build["identify"]["cnpj"] = substr($barCode, 0, 14);
        $identify->build["identify"]["nfe"] = substr($barCode, 14, strlen($barCode));
        return $identify->build;
    }
    /*------------------------------------------------>CERTIFICAR<-------------------------------------------------------*/
    public function images_factory_certify()
    {
        /*CURRENT CLASS*/
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        $identify->image = $this->image;
        /*LER COD BARRAS*/
        $get_factory = new Scanner_factory();
        $get_factory->image = $identify->image;
        $get_factory->build = $identify->build;
        /*OPEN CERTIFICADOS*/
        $certificado_path = $this->build["path_local"] . "cnt-files/cnt-assets/cert/" . str_replace("\"", "", $this->build["identify"]["cnpj"]) . ".pfx";
        $certificado_file = file_get_contents($certificado_path);
        openssl_pkcs12_read($certificado_file, $certificado, base64_decode(CERTIFICADOS));
        $cert = $certificado["cert"];
        $pkey = $certificado["pkey"];
        /*CRIAR .CER*/
        $path_cert = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]) . "cert/";
        file_put_contents(
            $path_cert . str_replace("\"", "", $this->build["identify"]["cnpj"]) . ".crt",
            $certificado['pkey'] . $certificado['cert']
        );
        /*ADD .CRT TO BUILD*/
        $get_factory->build["crt"]["path"] = $path_cert . str_replace("\"", "", $this->build["identify"]["cnpj"]) . ".crt";
        $CertPriv = openssl_x509_parse(openssl_x509_read($cert));
        $get_factory->build["crt"]["text"] = $CertPriv;
        /*TO ZBAR*/
        if ($this->build["identify"]["origin"] == "ZBAR") {
            $get_factory->factory_turn_image_to_pdf();
            $get_factory->factory_erase_link();
            return $get_factory->build;
        }
        /*TO TESSERACT*/
        if ($this->build["identify"]["origin"] == "TESSERACT") {
            $identify->build = $get_factory->build;
            return $identify->build;
        }
        /*TO SAVE TESSERACT*/
        if ($this->build["identify"]["origin"] == "SAVE-TESSERACT") {
            $get_factory->factory_turn_image_to_pdf();
            $get_factory->factory_erase_link();
        }
    }
    /*SALVAR TESSERACT*/
    public function images_factory_tesseract_save()
    {
        $saveFactory = new Scanner_image_factory();
        $saveFactory->build = $this->build;
        $saveFactory->build["identify"] = array();
        $saveFactory->build["identify"]["origin"] = "SAVE-TESSERACT";
        for ($i = 0; $i < count($this->image); $i++) {
            $identy = $this->image[$i];
            $dados_ocultos = json_decode($identy["oculta"], true);
            $saveFactory->image = $identy;
            $saveFactory->build["identify"]["cnpj"] = $identy["cnpj"];
            $saveFactory->build["identify"]["nfe"] = $identy["nfe"];
            $saveFactory->build["scannerID"] = $dados_ocultos["scanner"];
            $saveFactory->image = $dados_ocultos["image"];
            return json_encode($saveFactory);
            //return $saveFactory->images_factory_certify();
        }
    }


    /*DEPRECATED*/
    /*SET IDENTIFY PARA TESSERACT*/
    public function images_factory_identify_tesseract($tessa)
    {
        $identify = new Scanner_image_factory();
        $identify->image = $this->image;
        $identify->build = $this->build;
        $identify->build["identify"] = array();
        $identify->build["identify"]["origin"] = "TESSERACT";
        if (is_null($tessa["cnpj"])) $identify->build["identify"]["cnpj"] = "";
        if (is_null($tessa["nfe"])) $identify->build["identify"]["nfe"] = "";

        if ((!is_null($tessa["cnpj"])) or (!is_null($tessa["nfe"]))) {
            $identify->build["identify"]["raw"] = $tessa["cnpj"] . $tessa["nfe"];
        } else {
            $identify->build["identify"]["raw"] = "";
        }

        if (!is_null($tessa["cnpj"])) $identify->build["identify"]["cnpj"] = $tessa["cnpj"];
        if (!is_null($tessa["nfe"])) $identify->build["identify"]["nfe"] = $tessa["nfe"];
        return $identify->build;
    }
    /*DEPRECATED*/
}
