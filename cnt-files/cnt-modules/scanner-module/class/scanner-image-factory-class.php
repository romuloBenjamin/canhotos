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
                if ($image_sizes["width"] < $image_sizes["height"]) {
                    $get_factory->factory_rotate(90, "path_process", "path_process");
                    $get_factory->factory_rotate(90, "path_process", "path_process", "local");
                    $image_sizes = $get_factory->factory_getSizes();
                }
                $get_factory->build["sizes"] = $image_sizes;
                echo json_encode($get_factory);
                /*UPDATE BUILD*/
                //$factory->image = $get_factory->image;
                //$factory->build = $get_factory->build;
                /*SET ORIENTATION OF SAMPLES*/
                //$builds = $factory->images_factory_orientations();
                //$factory->build = $builds;
                //$factory->swit = "init-identify-nfe";
                //return $factory->images_factory();
                break;
            case 'init-identify-nfe':
                $factory->build = $this->build;
                $factory->image = $this->image;
                /*IDENTIFY ZBAR | TESSERACT | TESSERACT-FAILS*/
                if ($factory->build["identify"]["origin"] == "ZBAR") {
                    $barCode = $this->build["identify"]["raw"];
                    sleep(1);
                    $zbar_builds = $factory->images_factory_identify($barCode);
                    echo json_encode(array("zbar:"));
                    echo json_encode($zbar_builds);
                }/*
                if ($factory->build["identify"]["origin"] == "TESSERACT") {
                    $tessa = $this->build["identify"]["raw"];
                    sleep(1);
                    $factory->swit = "tesseract-ident-steps";
                    echo json_encode($this);
                    //$tessa = $factory->images_factory();
                    //$factory->build = $tessa;
                }*/
                //$factory->build["crop_index"] = 3;
                //$new_builds = $factory->images_factory_init_identify();
                //$factory->build = $new_builds;
                //if ($factory->build["identify"]["raw"] == "TESSERACT") {
                //$factory->swit = "tesseract-ident-steps";
                //$tessa = $factory->images_factory();
                //$factory->build = $tessa;
                //}
                //$factory->swit = "start-to-certify";
                //return $factory->images_factory();
                break;
            case 'tesseract-ident-steps':
                /*SOMENTE PARA TESSERACT IDENT*/
                $factory->build = $this->build;
                $factory->image = $this->image;
                $ident = $factory->tesseract_ident_steps();
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

    /*------------------------------------------>LEITOR ZBAR<------------------------------------------*/
    /*ORIENTATIONS*/
    public function images_factory_orientations()
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
        /*BAR CODE READSAMPLES*/
        $barCode = $get_factory->factory_read_barcode();
        if ($barCode["code"] == 200) {
            /*GET NEW SIZES*/
            $image_sizes = $get_factory->factory_getSizes();
            $get_factory->build["sizes"] = $image_sizes;
            $get_factory->build["identify"]["origin"] = "ZBAR";
            $get_factory->build["identify"]["raw"] = $barCode["text"];
        }
        /*NÂO LOCALIZADO BARCODE*/
        if ($barCode["code"] != 200) {
            /*ROTATE AGAIN AND GET SIZES*/
            $get_factory->factory_rotate(180, "path_process", "path_process");
            $get_factory->factory_rotate(180, "path_process", "path_process", "local");
            /*CRAETE NEW SAMPLE*/
            $get_factory->factory_identify_sample_init(true);
            $secondTry = $get_factory->factory_read_barcode();
            if ($secondTry["code"] == 400) {
                $get_factory->build["crop_index"] = 4;
                $get_factory->factory_identify_sample_init(true);
                $get_factory->build["identify"]["origin"] = "TESSERACT";
            }
        }
        $factory->build = $get_factory->build;
        $factory->image = $get_factory->image;
        return $get_factory->build;
    }
    /*INIT IDENTIFY*/
    public function images_factory_init_identify()
    {
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        $identify->image = $this->image;
        /*SCAN FACTORY*/
        $factory = new Scanner_factory();
        $factory->image = $identify->image;
        $factory->build = $identify->build;
        /*INIT IDENTIFICACAO*/
        $barCode = $factory->factory_read_barcode();
        if (($barCode["code"] == 200) && ($barCode["text"] == "")) return $identify->images_factory_forca();
        if ($barCode["code"] == 400) return $identify->images_factory_forca();
        return $identify->images_factory_identify($barCode);
    }
    /*IDENTIFY NFE*/
    public function images_factory_get_nfe($forcar = 0)
    {
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        $identify->image = $this->image;
        /*LER COD BARRAS*/
        $get_factory = new Scanner_factory();
        $get_factory->image = $identify->image;
        $get_factory->build = $identify->build;
        $barCode = $get_factory->factory_read_barcode();
        /*IF NÃO LOCALIZADO COD BARRAS*/
        if ($barCode["code"] == 400) {
            return $identify->images_factory_forca($forcar);
        }
        if ($barCode["code"] == 200) {
            if ($barCode["text"] == "") return $identify->images_factory_forca($forcar);
            return $identify->images_factory_identify($barCode);
        }
    }
    /*---------------------------------------------->FORÇAR IDENTIFICAÇÃO<----------------------------------------------------*/
    /*FORÇAR IDENTIFICATIONS*/
    public function images_factory_forca($try = 0)
    {
        $forcar = new Scanner_image_factory();
        $forcar->build = $this->build;
        $forcar->image = $this->image;
        /*LER COD BARRAS*/
        $get_factory = new Scanner_factory();
        $get_factory->image = $forcar->image;
        $get_factory->build = $forcar->build;
        /*IDENT VIA BARCODE*/
        if ($try == 0) {
            $try++;
            $get_factory->factory_rotate(180, "path_process", "path_process");
            $get_factory->factory_rotate(180, "path_process", "path_process", "local");
            $get_factory->factory_identify_sample_init(true);
            return $forcar->images_factory_get_nfe($try);
        }
        /*IDENT VIA TESSERACT*/
        if ($try == 1) {
            /*NEW IDENT CROP*/
            $get_factory->build["crop_index"] = 5;
            $forcar->build["crop_index"] = 5;
            /*ROTATE AGAIN*/
            $get_factory->factory_rotate(180, "path_process", "path_process");
            $get_factory->factory_rotate(180, "path_process", "path_process", "local");
            /*NEW DIMENSIONS*/
            $dimensions = $get_factory->factory_crop_dimensions();
            $get_factory->build["crop"] = $dimensions;
            $forcar->build["crop"] = $dimensions;
            /*NEW SAMPLE*/
            $get_factory->factory_identify_sample_init(true);
            $reads_raws = $get_factory->factory_read_sample();
            /*FILL TESSERACT READS*/
            $reads_raws = trim(str_replace(REMOVE_SPECIALS, "", $reads_raws["data"]));
            $reads = explode(" ", $reads_raws);
            $get_factory->build["tesseract_read"] = $reads;
            $forcar->build["tesseract_read"] = $reads;
            $forcar->build["identify"] = array("raw" => "TESSERACT");
            return $forcar->build;
        }
    }
    /*---------------------------------------------->LEITOR TESSERACT<----------------------------------------------*/
    public function tesseract_ident_steps()
    {
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        $identify->image = $this->image;
        /*LER COD BARRAS*/
        $get_factory = new Scanner_factory();
        $get_factory->image = $identify->image;
        $get_factory->build = $identify->build;
        $nArray = array("raw" => "", "cnpj" => 0, "nfe" => 0);
        if (array_intersect(SALES_PRIMARY_KNOW_NAMES, $get_factory->build["tesseract_read"])) {
            $nArray["cnpj"] = $identify->getSalesCNPJ();
            $nArray["nfe"] = $identify->getNFES();
            return $identify->images_factory_identify_tesseract($nArray);
        } else if (array_intersect(SANDALO_PRIMARY_KNOW_NAMES, $get_factory->build["tesseract_read"])) {
            $nArray["cnpj"] = $identify->getSandaloCNPJ();
            $nArray["nfe"] = $identify->getNFES();
            return $identify->images_factory_identify_tesseract($nArray);
        } else if (array_intersect(DONA_PRIMARY_KNOW_NAMES, $get_factory->build["tesseract_read"])) {
            $nArray["cnpj"] = DONA_DESCARTAVEIS["CNPJ"];
            $nArray["nfe"] = $identify->getNFES();
            return $identify->images_factory_identify_tesseract($nArray);
        } else {
            /*UPDATE PARAMETER TO READ*/
            $get_factory->build["gamma_index"] = floatval($get_factory->build["gamma_index"]) + .022;
            $get_factory->build["specials"]["active"] = true;
            $get_factory->build["specials"]["sharpen"] = true;
            $get_factory->build["specials"]["sharpen_data"][0] = 1;
            $get_factory->build["specials"]["sharpen_data"][1] = floatval($get_factory->build["specials"]["sharpen_data"][1]) + .010;
            /*RECREATE SAMPLE AND READ*/
            $get_factory->factory_identify_sample_init(true);
            $reads_raws = $get_factory->factory_read_sample();
            /*UPDATE TESSERACT READS*/
            $reads_raws = trim(str_replace(REMOVE_SPECIALS, "", $reads_raws["data"]));
            $reads = explode(" ", $reads_raws);
            $get_factory->build["tesseract_read"] = $reads;
            $identify->build["tesseract_read"] = $reads;
            /*UPDATE BUILD*/
            $identify->build = $get_factory->build;
            /*FINALIZA*/
            if ($identify->build["gamma_index"] < 1.600) return $identify->tesseract_ident_steps();
            if ($identify->build["gamma_index"] > 1.600) {
                $identify->build["identify"] = array();
                $identify->build["identify"]["origin"] = "FAILS-TESSERACT";
                $identify->build["identify"]["raw"] = "";
                $identify->build["identify"]["cnpj"] = "";
                $identify->build["identify"]["nfe"] = "";
                return $identify->build;
            }
        }
    }
    /*GET CNPJS -> SALES*/
    public function getSalesCNPJ($try = 0)
    {
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        $identify->image = $this->image;
        /*LER COD BARRAS*/
        $get_factory = new Scanner_factory();
        $get_factory->image = $identify->image;
        $get_factory->build = $identify->build;
        /*RESTAURAR PARAMETER TO NEW SEARCH*/
        if ($try == 0) {
            $get_factory->build["gamma_index"] = .100;
            $get_factory->build["specials"]["sharpen_data"][1] = .5;
            $identify->build = $get_factory->build;
        }
        /*CONFIG PARAMETER TO SEARCH*/
        if ($try != 0) $get_factory->build["gamma_index"] = floatval($get_factory->build["gamma_index"]) + .05;
        $get_factory->build["specials"]["enhance"] = true;
        $get_factory->build["specials"]["sharpen_data"][1] = floatval($get_factory->build["specials"]["sharpen_data"][1]) + .010;
        /*SET DIMENSIONS TO CROP*/
        $get_factory->build["crop_index"] = 5;
        $dimensions = $get_factory->factory_crop_dimensions();
        $get_factory->build["crop"] = $dimensions;
        $identify->build = $get_factory->build;
        /*CREATE NEW SAMPLE*/
        $get_factory->factory_identify_sample_init(true);
        $reads_raws = $get_factory->factory_read_sample();
        /*UPDATE TESSERACT READS*/
        $reads_raws = trim(str_replace(REMOVE_SPECIALS, "", $reads_raws["data"]));
        $reads = explode(" ", $reads_raws);
        $get_factory->build["tesseract_read"] = $reads;
        $identify->build["tesseract_read"] = $reads;
        /*IDENTIFICAÇÃO DE DADOS*/
        if (array_intersect(SALES_EQUIP["SECONDARY_KNOW_NAMES"], $identify->build["tesseract_read"])) {
            return SALES_EQUIP["CNPJ"];
        } else {
            $try++;
            if ($get_factory->build["gamma_index"] < 1.600) return $identify->getSalesCNPJ($try);
        }
    }
    /*GET CNPJS -> SANDALO*/
    public function getSandaloCNPJ($try = 0)
    {
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        $identify->image = $this->image;
        /*LER COD BARRAS*/
        $get_factory = new Scanner_factory();
        $get_factory->image = $identify->image;
        $get_factory->build = $identify->build;
        /*RESTAURAR PARAMETER TO NEW SEARCH*/
        if ($try == 0) {
            $get_factory->build["gamma_index"] = .100;
            $get_factory->build["specials"]["sharpen_data"][1] = .5;
            $identify->build = $get_factory->build;
        }
        /*CONFIG PARAMETER TO SEARCH*/
        if ($try != 0) $get_factory->build["gamma_index"] = floatval($get_factory->build["gamma_index"]) + .05;
        $get_factory->build["specials"]["enhance"] = true;
        $get_factory->build["specials"]["sharpen_data"][1] = floatval($get_factory->build["specials"]["sharpen_data"][1]) + .010;
        /*SET DIMENSIONS TO CROP*/
        $get_factory->build["crop_index"] = 5;
        $dimensions = $get_factory->factory_crop_dimensions();
        $get_factory->build["crop"] = $dimensions;
        $identify->build = $get_factory->build;
        /*CREATE NEW SAMPLE*/
        $get_factory->factory_identify_sample_init(true);
        $reads_raws = $get_factory->factory_read_sample();
        /*UPDATE TESSERACT READS*/
        $reads_raws = trim(str_replace(REMOVE_SPECIALS, "", $reads_raws["data"]));
        $reads = explode(" ", $reads_raws);
        $get_factory->build["tesseract_read"] = $reads;
        $identify->build["tesseract_read"] = $reads;
        if (array_intersect(SANDALO_EQUIP["SECONDARY_KNOW_NAMES"], $identify->build["tesseract_read"])) {
            return SANDALO_EQUIP["CNPJ"];
        } else {
            $try++;
            if ($identify->build["gamma_index"] > 1.600) return $identify->getSandaloCNPJ($try);
        }
    }
    /*IDENTIFY NFE*/
    public function getNFES($try = 0)
    {
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        $identify->image = $this->image;
        /*LER COD BARRAS*/
        $get_factory = new Scanner_factory();
        $get_factory->image = $identify->image;
        $get_factory->build = $identify->build;
        /*RESTAURAR PARAMETER TO NEW SEARCH*/
        if ($try == 0) {
            $get_factory->build["gamma_index"] = .100;
            $get_factory->build["specials"]["sharpen_data"][1] = .5;
            $identify->build = $get_factory->build;
        }
        /*CONFIG PARAMETER TO SEARCH*/
        if ($try != 0) $get_factory->build["gamma_index"] = floatval($get_factory->build["gamma_index"]) + .05;
        $get_factory->build["specials"]["enhance"] = true;
        $get_factory->build["specials"]["sharpen_data"][1] = floatval($get_factory->build["specials"]["sharpen_data"][1]) + .010;
        /*SET DIMENSIONS TO CROP*/
        $get_factory->build["crop_index"] = 4;
        $dimensions = $get_factory->factory_crop_dimensions();
        $get_factory->build["crop"] = $dimensions;
        $identify->build = $get_factory->build;
        /*CREATE NEW SAMPLE*/
        $get_factory->factory_identify_sample_init(true);
        $reads_raws = $get_factory->factory_read_sample();
        /*UPDATE TESSERACT READS*/
        $reads_raws = trim(str_replace(ONLY_NUMBERS, "", mb_strtolower($reads_raws["data"], "UTF-8")));
        $reads = strlen(str_replace(" ", "", $reads_raws));
        /*IDENT NFE*/
        if ($reads >= 6) {
            return $reads;
        } else {
            $try++;
            return $identify->getNFES($try);
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
    /*CERTIFICAR*/
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
        $certificado_path = $this->build["path_local"] . "cnt-files/cnt-assets/cert/" . $this->build["identify"]["cnpj"] . ".pfx";
        $certificado_file = file_get_contents($certificado_path);
        openssl_pkcs12_read($certificado_file, $certificado, base64_decode(CERTIFICADOS));
        $cert = $certificado["cert"];
        $pkey = $certificado["pkey"];
        /*CRIAR .CER*/
        $path_cert = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]) . "cert/";
        file_put_contents(
            $path_cert . $this->build["identify"]["cnpj"] . ".crt",
            $certificado['pkey'] . $certificado['cert']
        );
        /*ADD .CRT TO BUILD*/
        $get_factory->build["crt"]["path"] = $path_cert . $this->build["identify"]["cnpj"] . ".crt";
        $CertPriv = openssl_x509_parse(openssl_x509_read($cert));
        $get_factory->build["crt"]["text"] = $CertPriv;
        /*TO ZBAR*/
        if ($this->build["identify"]["origin"] == "ZBAR") {
            $get_factory->factory_turn_image_to_pdf();
            $get_factory->factory_erase_link();
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
        return;
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
}
