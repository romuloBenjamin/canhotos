<?php
class Scanner_factory
{
    var $entry;
    var $image;
    var $build;

    public function __construct()
    {
        $this->build = array();
    }

    public function factory_compound()
    {
        $factory = new Scanner_factory();
        $factory->entry = $this->entry;
        $factory->image = $this->image;
        switch ($this->swit) {
            case 'verificar-certificados-expirados':
                $builds = $factory->factory_mergeBuild($factory->build, $this->build);
                $factory->build = $builds;
                return $factory->factory_expired_digitals_signs();
                break;
                /*default:break; */
        }
    }

    /*FACTORY MERGE BUILDS*/
    public function factory_mergeBuild($builds1, $builds2)
    {
        return array_merge($builds1, $builds2);
    }

    /*FACTORY GET CROP IMAGE*/
    public function factory_crop($width = 600, $height = 100, $matrix = array(0, 0), $align = "middle")
    {
        $dimensions = $this->build["sizes"];
        /*FORMULA CROP*/
        $formula_dimensions_x = ($dimensions["width"] / 2) - ($width / 2);
        if ($align == "middle") $dimensions_x = $formula_dimensions_x + $matrix[0];
        if ($align == "topLeft") $dimensions_x = $matrix[0];
        $dimensions_y = $matrix[1];
        return array($width, $height, $dimensions_x, $dimensions_y);
    }

    /*FACTORY GET CROP DIMENSIONS*/
    public function factory_crop_dimensions()
    {
        $dimens = new Scanner_factory();
        $dimens->build = $this->build;

        if ($dimens->build["crop_index"] == 0) $dimensions = array(1000, 400, array(850, 600), "topLeft");
        if ($dimens->build["crop_index"] == 1) $dimensions = array(1400, 200, array(20, 400), "middle");
        if ($dimens->build["crop_index"] == 2) $dimensions = array(1400, 400, array(20, 100), "middle");
        if ($dimens->build["crop_index"] == 3) $dimensions = array(1400, 400, array(150, 100), "topLeft");
        if ($dimens->build["crop_index"] == 4) $dimensions = array(600, 400, array(1800, 100), "middle");
        if ($dimens->build["crop_index"] == 5) $dimensions = array(2300, 500, array(10, 5), "middle");

        return $dimens->factory_crop($dimensions[0], $dimensions[1], $dimensions[2], $dimensions[3]);
    }

    /*SCAN DIR*/
    public function factory_scandir($where = "path_origin")
    {
        $nArray = array();
        $folder_to_scan = $this->build["path_rede"] . str_replace("./", "", $this->build[$where]) . "\\" . $this->build["scannerID"] . "\\" . $this->build["who"];
        $array_files = scandir($folder_to_scan);
        for ($i = 2; $i < count($array_files); $i++) {
            $files = $array_files[$i];
            $xplode = explode(".", $files);
            if (count($xplode) > 1) $nArray[] = $files;
        }
        return $nArray;
    }

    /*EXPLODE*/
    public function factory_xplode($array)
    {
        $nArray = array();
        $xplode = explode(" ", $array);
        for ($i = 0; $i < count($xplode); $i++) {
            if ($xplode[$i] != "") {
                $nArray[] = $xplode[$i];
            }
        }
        return $nArray;
    }

    /*ERASE DIR*/
    public function factory_erase_dir($where = "path_results")
    {
        $path_to_delete = $this->build["path_rede"] . str_replace("./", "", $this->build[$where]) . "\\" . $this->build["scannerID"];
        /*GET FILES IN FOLDER TO DELETE*/
        $transp = new Scanner_factory();
        $transp->build = $this->build;
        $files_to_remove = $transp->factory_scandir($where);
        for ($i = 0; $i < count($files_to_remove); $i++) {
            unlink($path_to_delete . "\\" . $files_to_remove[$i]);
        }
        return;
    }
    /*REMOVE IMAGE FROM FOLDERS*/
    public function factory_erase_link()
    {
        $path_to_delete_rede = $this->build["path_rede"] . str_replace("./", "", $this->build["path_origin"]) . "\\" . $this->build["scannerID"];
        $path_to_delete_local = $this->build["path_local"] . "cnt-files/images/scanner/" . $this->build["scannerID"];
        /*PATH TO SERVER*/
        $path_server_image_process = $path_to_delete_rede . "\\" . str_replace("./scanner/", "", $this->build["path_process"]);
        $path_server_image_results = $path_to_delete_rede . "\\" . str_replace("./scanner/", "", $this->build["path_results"]);
        $path_scan_image = $path_to_delete_rede;
        /*PATH TO LOCAL*/
        $path_local_image_process = $path_to_delete_local . "/" . str_replace("./scanner/", "", $this->build["path_process"]);
        $path_local_image_results = $path_to_delete_local . "/" . str_replace("./scanner/", "", $this->build["path_results"]);
        /*GET FILES IN FOLDER TO DELETE*/
        $transp = new Scanner_factory();
        $transp->build = $this->build;
        /*REMOVE PROCESS & RESULTS SERVER*/
        unlink($path_server_image_process . "\\" . $this->image);
        unlink($path_server_image_results . "\\" . $this->image);
        /*REMOVE PROCESS & RESULTS LOCAL*/
        unlink($path_local_image_process . "/" . $this->image);
        unlink($path_local_image_results . "/" . $this->image);
        /*REMOVE TXT*/
        if (file_exists(str_replace(".jpeg", ".txt", $this->image))) unlink($path_local_image_results . "/" . str_replace(".jpeg", ".txt", $this->image));
        /*REMOVE PDF LOCAL FILE*/
        $pdf_local_file = $path_local_image_results . "/" . $this->build["identify"]["cnpj"] . str_pad($this->build["identify"]["nfe"], 8, STR_PAD_LEFT) . ".pdf";
        if (file_exists($pdf_local_file)) @unlink($pdf_local_file);
        /*REMOVE FILE TIF*/
        $to_remove = scandir($path_scan_image);
        $formatos = array(".", "..");
        for ($i = 0; $i < count($to_remove); $i++) {
            if (!in_array($to_remove[$i], $formatos)) {
                $xplode = explode(".", $to_remove[$i]);
                if (count($xplode) > 1) @unlink($path_scan_image . "\\" . str_replace(".jpeg", ".tif", $this->image));
            }
        }
        return;
    }
    /*FACTORY GET IMAGE SIZES*/
    public function factory_getSizes($where = "path_process")
    {
        $image_open_path = $this->build["path_rede"] . str_replace("./", "", $this->build["path_origin"]);
        $image_get_path = $image_open_path . "\\" . $this->build["scannerID"] . "\\" . $this->build["who"] . str_replace("./scanner/", "\\", $this->build[$where]);
        $nArray = array();
        /*IMAGICK*/
        $imagick = new Imagick();
        $imagick->readImage($image_get_path . "\\" . $this->image);
        $nArray["width"] = $imagick->getImageWidth();
        $nArray["height"] = $imagick->getImageHeight();
        return $nArray;
    }

    /*FACTORY ROTATE IMAGE*/
    public function factory_rotate($deg, $from = "path_process", $to = "path_results", $where = "rede")
    {
        $image_open_path = $this->build["path_rede"] . str_replace("./", "", $this->build["path_origin"]);
        $image_open_local = "../../../images/scanner/" . $this->build["scannerID"] . "/" . $this->build["who"];
        //PUT IMAGES
        $image_put_path_from = $image_open_path . "\\" . $this->build["scannerID"] . "\\" . $this->build["who"] . str_replace("./scanner/", "\\", $this->build[$from]);
        $image_put_path_to = $image_open_path . "\\" . $this->build["scannerID"] . "\\" . $this->build["who"] . str_replace("./scanner/", "\\", $this->build[$to]);
        $image_put_path_to_local = $image_open_local . "/" . str_replace("./scanner/", "", $this->build["path_process"]);
        /*ROTATE IMAGE WHERE*/
        if ($where == "rede") {
            //IMAGE MAGICK
            $imagick = new Imagick();
            $imagick->readImage($image_put_path_from . "\\" . $this->image);
            $imagick->rotateImage("#fff", $deg);
            file_put_contents($image_put_path_to . "\\" . $this->image, "");
            file_put_contents($image_put_path_to . "\\" . $this->image, $imagick);
            $imagick->clear();
            $imagick->destroy();
        }
        /*ROTATE IMAGE WHERE*/
        if ($where == "local") {
            //IMAGE MAGICK LOCAL
            $imagick2 = new Imagick();
            $imagick2->readImage($image_put_path_to . "\\" . $this->image);
            file_put_contents($image_put_path_to_local . "/" . $this->image, "");
            file_put_contents($image_put_path_to_local . "/" . $this->image, $imagick2);
            $imagick2->clear();
            $imagick2->destroy();
        }
        return;
    }

    /*CREATE SAMPLES*/
    public function factory_identify_sample_init($identify = false, $where = "path_process")
    {
        $image_and_format = explode(".", $this->image);
        $image_open_path = $this->build["path_rede"] . str_replace("./", "", $this->build["path_origin"]) . "\\" . $this->build["scannerID"] . "\\" . $this->build["who"];
        $image_open_local = "../../../images/scanner/" . $this->build["scannerID"] . "/";
        $image_open_local2 = $image_open_local . $this->build["who"] . "/";
        if (!is_dir($image_open_local)) mkdir($image_open_local, "0777");
        if (!is_dir($image_open_local2)) mkdir($image_open_local2, "0777");
        //PUT IMAGES
        if ($where == "path_origin") {
            $image_put_path_from = $image_open_path;
            $image_put_path_to = $image_open_path . str_replace("./scanner/", "\\", $this->build["path_process"]);
            $image_put_path_to_local = $image_open_local2 . "/" . str_replace("./scanner/", "", $this->build["path_process"]);
        }
        if ($where == "path_process") {
            $image_put_path_from = $image_open_path . str_replace("./scanner/", "\\", $this->build["path_process"]);
            $image_put_path_to = $image_open_path . str_replace("./scanner/", "\\", $this->build["path_results"]);
            $image_put_path_to_local = $image_open_local2 . "/" . str_replace("./scanner/", "", $this->build["path_results"]);
        }
        if (!is_dir($image_put_path_to)) mkdir($image_put_path_to, "0777");
        if (!is_dir($image_put_path_to_local)) mkdir($image_put_path_to_local, "0777");
        /*IMAGICK*/
        try {
            $imagick = new Imagick();
            $imagick->readImage($image_put_path_from . "\\" . $this->image);
            $imagick->setResolution(1000, 500);
            $imagick->setImageFormat("jpeg");
            $imagick->despeckleImage();
            /*IDENTIFY*/
            if ($identify == true) {
                $crop_image = $this->build["crop"];
                $imagick->despeckleImage();
                /*SPECIALS*/
                if ($this->build["specials"]["active"] == true) {
                    if ($this->build["specials"]["greyScale"] == true) $imagick->setImageType(Imagick::IMGTYPE_GRAYSCALEMATTE);
                    if ($this->build["specials"]["enhance"] == true) $imagick->enhanceImage();
                    if ($this->build["specials"]["equalize"] == true) $imagick->equalizeImage();
                    if ($this->build["specials"]["depth"] == true) $imagick->setImageDepth($this->build["specials"]["depth_data"]);
                    if ($this->build["specials"]["sharpen"] == true) {
                        $sharpen_data = $this->build["specials"]["sharpen_data"];
                        $imagick->sharpenImage($sharpen_data[0], $sharpen_data[1]);
                    }
                }
                $imagick->blurImage($this->build["blur_indexes"][0], $this->build["blur_indexes"][1]);
                $imagick->gammaImage($this->build["gamma_index"], Imagick::CHANNEL_ALL);
                $imagick->cropImage($crop_image[0], $crop_image[1], $crop_image[2], $crop_image[3]);
            }
            /*CRIAR NOVO ARQUIVO JPEG*/
            if ($where == "path_origin") {
                if (file_exists($image_put_path_to . "\\" . $image_and_format[0] . ".jpeg")) file_put_contents($image_put_path_to . "\\" . $image_and_format[0] . ".jpeg", "");
                file_put_contents($image_put_path_to . "\\" . $image_and_format[0] . ".jpeg", $imagick);
                /*COPIA LOCAL*/
                if (file_exists($image_put_path_to_local . "\\" . $image_and_format[0] . ".jpeg")) file_put_contents($image_put_path_to_local . "\\" . $image_and_format[0] . ".jpeg", "");
                file_put_contents($image_put_path_to_local . "\\" . $image_and_format[0] . ".jpeg", $imagick);
            } else {
                if (file_exists($image_put_path_to . "\\" . $image_and_format[0] . ".jpeg")) file_put_contents($image_put_path_to . "\\" . $image_and_format[0] . ".jpeg", "");
                file_put_contents($image_put_path_to . "\\" . $image_and_format[0] . ".jpeg", $imagick);
                /*COPIA LOCAL*/
                if (file_exists($image_put_path_to_local . "\\" . $image_and_format[0] . ".jpeg")) file_put_contents($image_put_path_to_local . "\\" . $image_and_format[0] . ".jpeg", "");
                file_put_contents($image_put_path_to_local . "\\" . $image_and_format[0] . ".jpeg", $imagick);
            }
            $imagick->clear();
            $imagick->destroy();
        } catch (\Throwable $th) {
            return $th;
        }
        return;
    }

    /*CREATE SAMPLE TO FORCE IDENTIFICATIONS*/
    public function factory_identify_by_force()
    {
        var_dump($this);
    }

    /*READ SAMPLE*/
    public function factory_read_sample()
    {
        $tesseract = new Scanner_tesseract();
        $tesseract->build = $this->build;
        $tesseract->entry = $this->image;
        return $tesseract->tesseract_ocr();
    }

    /*READ BARCODE*/
    public function factory_read_barcode()
    {
        $path_file_ZBAR = $this->build["path_rede"] . str_replace("./", "", $this->build["path_origin"]) . "\\" . $this->build["scannerID"] . "\\" . $this->build["who"];
        $path_file_ZBAR = $path_file_ZBAR . str_replace("./scanner/", "\\", $this->build["path_results"]);
        /*LER COD BARRAS*/
        $nArray = array();
        $ZbarDecoder = new RobbieP\ZbarQrdecoder\ZbarDecoder();
        $ZbarDecoder->setPath("c:\\ZBar\\bin");
        $result = $ZbarDecoder->make($path_file_ZBAR . "\\" . $this->image);
        $nArray["code"] = $result->code;
        $nArray["text"] = $result->text;
        return $nArray;
    }
    /*CREATE PDF FILE FOM IMAGE*/
    public function factory_turn_image_to_pdf()
    {
        $path_to_rede = $this->build["path_rede"] . str_replace("./", "", $this->build["path_origin"]) . "\\" . $this->build["scannerID"];
        $path_to_crt = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]);
        $path_tmp = $this->build["path_local"] . "/cnt-files/images/scanner/" . $this->build["scannerID"] . "/results";
        /*IMAGE DATA & PATH*/
        $path_save_file = $path_to_rede;
        $path_open_file = $path_to_rede . str_replace("./scanner/", "\\", $this->build["path_process"]);
        $format_and_extensions = explode(".", $this->image);
        /*CRIATE FOLDER IF NOT EXIST*/
        if (!is_dir($path_save_file . "/" . date('Y'))) mkdir($path_save_file . "/" . date('Y'), 0777);
        if (!is_dir($path_save_file . "/" . date('Y') . "/" . date('m'))) mkdir($path_save_file . "/" . date('Y') . "/" . date('m'), 0777);
        if (!is_dir($path_save_file . "/" . date('Y') . "/" . date('m') . "/" . date('d'))) mkdir($path_save_file . "/" . date('Y') . "/" . date('m') . "/" . date('d'), 0777);
        if (!is_dir($path_tmp)) mkdir($path_tmp, "0777");
        /*CREATE TMP*/
        $file_image = file_get_contents($path_open_file . "\\" . $this->image);
        file_put_contents($path_tmp . "/" . $this->image, $file_image);
        //TCPDF
        $tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $tcpdf->SetCreator(PDF_CREATOR);
        $tcpdf->SetAuthor('TI Sales Equip');
        $tcpdf->SetTitle($format_and_extensions[0]);
        // set margins
        $tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // set auto page breaks
        $tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //ADD PAGE
        $tcpdf->AddPage();
        // set JPEG quality
        $tcpdf->setJPEGQuality(99);
        // The '@' character is used to indicate that follows an image data stream and not an image file name
        //$pdf->Image('@' . $imgdata);
        $tcpdf->Image($path_tmp . "/" . $this->image, 2, 10, 200, 50, "jpeg", false);
        //CERTIFICAR PDF
        // set certificate file
        $certificate = 'file://' . $this->build["crt"]["path"];
        // set document signature
        $tcpdf->setSignature($certificate, $certificate, '', '', 2, '');
        $tcpdf->SetFont('helvetica', '', 5);
        // print a line of text
        $text1 = $this->build["crt"]["text"]["subject"]["CN"];
        $text2 = date("Y/m/d H:i");
        $text3 = "Cert. Canhoto NFe";
        $text4 = "Sao Paulo - Brasil";
        $tcpdf->MultiCell(40, 5, $text1, 1, "L", false, 1, null, 30);
        $tcpdf->Cell(40, 1, $text2, 0, 1);
        $tcpdf->Cell(40, 1, $text3, 0, 1);
        $tcpdf->Cell(40, 1, $text4, 0, 1);
        // create content for signature (image and/or text)
        $tcpdf->Image($path_tmp . "/" . $format_and_extensions[0] . '.png', 180, 60, 15, 15, 'PNG');
        // define active area for signature appearance
        $tcpdf->setSignatureAppearance(180, 60, 15, 15);
        // *** set an empty signature appearance ***
        $tcpdf->addEmptySignatureAppearance(180, 80, 15, 15);
        //CERTIFICAR PDF
        $fileName = $this->build["identify"]["cnpj"] . str_pad($this->build["identify"]["nfe"], 9, "0", STR_PAD_LEFT);
        $tcpdf->Output($path_tmp . "/" . $fileName . ".pdf", "F");
        $server = file_get_contents($path_tmp . "/" . $fileName . ".pdf");
        $path_save_file = $path_save_file . "/" . date('Y') . "/" . date('m') . "/" . date('d');
        if (file_exists($path_save_file . "\\" . $fileName . ".pdf")) file_put_contents($path_save_file . "\\" . $fileName . ".pdf", "");
        file_put_contents($path_save_file . "\\" . $fileName . ".pdf", $server);
        return;
    }
    /*VIEW IF DIGITAL SIGNS EXPIRED*/
    public function factory_expired_digitals_signs()
    {
        $factory = new Scanner_factory();

        $nArray = array();
        $certificado_path = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]);
        for ($i = 0; $i < count($this->entry); $i++) {
            if (file_exists($certificado_path . "cert/" . $this->entry[$i])) {
                $certificado_expirado = file_get_contents($certificado_path . "cert/" . $this->entry[$i]);
                openssl_pkcs12_read($certificado_expirado, $certificado, base64_decode(CERTIFICADOS));
                $certPriv = openssl_x509_parse(openssl_x509_read($certificado['cert']));
                $data_expired = date('d/m/Y', $certPriv['validTo_time_t']);
                $data_expired_xplode = array_reverse(explode("/", $data_expired));
                if (date('Y') <= $data_expired_xplode[0]) {
                    if (date('m') <= $data_expired_xplode[1]) {
                        if (date('d') <= $data_expired_xplode[2]) {
                            $total_de_dias_do_mes_atual = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
                            $total_dias_corridos = intval($total_de_dias_do_mes_atual) - intval(date('d'));
                            $total_dias_para_expirar = intval($total_dias_corridos) + intval($data_expired_xplode[2]);
                            $nArray[] = array(
                                "timer" => "Este certificado expira em " . $total_dias_para_expirar . " dias.",
                                "expire" => $data_expired,
                                "cnpj" => str_replace(".pfx", "", $this->entry[$i]),
                                "empresa" => explode(":", $certPriv["subject"]["CN"])[0]
                            );
                        } else {
                            $nArray[] = array(
                                "timer" => "Certificado Expirado!",
                                "expire" => $data_expired,
                                "cnpj" => str_replace(".pfx", "", $this->entry[$i]),
                                "empresa" => explode(":", $certPriv["subject"]["CN"])[0]
                            );
                        }
                    } else {
                        $nArray[] = array(
                            "timer" => "Este Certificado vence este Mês!",
                            "expire" => $data_expired,
                            "cnpj" => str_replace(".pfx", "", $this->entry[$i]),
                            "empresa" => explode(":", $certPriv["subject"]["CN"])[0]
                        );
                    }
                } else {
                    $nArray[] = array(
                        "timer" => "Certificado Expirado!",
                        "expire" => $data_expired,
                        "cnpj" => str_replace(".pfx", "", $this->entry[$i]),
                        "empresa" => explode(":", $certPriv["subject"]["CN"])[0]
                    );
                }
            }
        }
        return json_encode($nArray);
    }

    /*GET EMPRESA NAME FROM CNPJ*/
    public function get_empresa_cnpj($cnpj)
    {
        if (SALES_EQUIP["CNPJ"] == $cnpj) {
            return SALES_EQUIP["SITE"];
        } else if (SALES_IND["CNPJ"] == $cnpj) {
            return SALES_IND["SITE"];
        } else if (COMERCIAL_SANDALO["CNPJ"] == $cnpj) {
            return COMERCIAL_SANDALO["SITE"];
        } else if (SANDALO_EQUIP["CNPJ"] == $cnpj) {
            return SANDALO_EQUIP["SITE"];
        } else if (DONA_DESCARTAVEIS["CNPJ"] == $cnpj) {
            return DONA_DESCARTAVEIS["SITE"];
        } else {
            return "nada";
        }
    }
}
