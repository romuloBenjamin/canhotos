<?php
class Scanner_factory
{
    var $entry;
    var $image;
    var $build;
    var $reader;
    var $certificated;

    public function __construct()
    {
        $this->dir_scan = array();
        $this->files = array();
        $this->reader = new stdClass();
        $this->reader->zbar = "";
        $this->reader->tesseract = "";
        /*CERTIFICADO VENCIDO*/
        $this->certificated = new stdClass();
        $this->certificated->timer = "";
        $this->certificated->expire = "";
        $this->certificated->cnpj = "";
        $this->certificated->empresa = "";
        $this->certificated->dias_p_expirar = "";
    }

    /*SCANNER PASTA*/
    public function scanear_pasta_emrede()
    {
        $path_to_scan = $this->build["path"][$this->entry["where"]][$this->entry["path"]];
        if (is_dir($path_to_scan)) $this->dir_scan = scandir($path_to_scan);
        return;
    }

    /*REMOVE FILES*/
    public function prepare_remove_files()
    {
        /*REMOVE FILES FROM REDE*/
        (file_exists($this->build["path"]["rede"]["usuario"] . "\\" . $this->image)) ? unlink($this->build["path"]["rede"]["usuario"] . "\\" . $this->image) : "";
        (file_exists($this->build["path"]["rede"]["process"] . "\\" . $this->build["images"]->from)) ? unlink($this->build["path"]["rede"]["process"] . "\\" . $this->build["images"]->from) : "";
        (file_exists($this->build["path"]["rede"]["results"] . "\\" . $this->build["images"]->to)) ? unlink($this->build["path"]["rede"]["results"] . "\\" . $this->build["images"]->to) : "";
        (file_exists($this->build["path"]["rede"]["results"] . "\\" . str_replace(".jpeg", ".txt", $this->build["images"]->to))) ? unlink($this->build["path"]["rede"]["results"] . "\\" . str_replace(".jpeg", ".txt", $this->build["images"]->to)) : "";
        /*REMOVE FILES FROM LOCAL*/
        (file_exists($this->build["path"]["local"]["process"] . "/" . $this->build["images"]->from)) ? unlink($this->build["path"]["local"]["process"] . "/" . $this->build["images"]->from) : "";
        (file_exists($this->build["path"]["local"]["results"] . "/" . $this->build["images"]->to)) ? unlink($this->build["path"]["local"]["results"] . "/" . $this->build["images"]->to) : "";
        /*VISUALIZAR*/
        return;
    }
    /*WIPE DIRECTORIES*/
    public function prepare_wip_directories()
    {
        /*WIP FILES TESSERACT*/
        $folder_tesseract = $this->build["path"]["rede"]["origin"] . "\\" . $this->build["user"];
        (file_exists($folder_tesseract . "\\" . str_replace(".jpeg", ".tif", $this->image))) ? unlink($folder_tesseract . "\\" . str_replace(".jpeg", ".tif", $this->image)) : "";
        /*WIP FILES TESSERACT -> txt*/
        $folder_tesseract_txt = $this->build["path"]["rede"]["process"];
        (file_exists($folder_tesseract_txt . "\\" . str_replace(".jpeg", ".txt", $this->image))) ? unlink($folder_tesseract_txt . "\\" . str_replace(".jpeg", ".txt", $this->image)) : "";
        /*WIP PROCESS LOCAL -> PROCESS*/
        $folder_tesseract_local = $this->build["path"]["local"]["process"];
        (file_exists($folder_tesseract_local . "/" . $this->image)) ? unlink($folder_tesseract_local . "/" . $this->image) : "";
        /*WIP PROCESS LOCAL -> RESULT*/
        $folder_tesseract_local2 = $this->build["path"]["local"]["results"];
        (file_exists($folder_tesseract_local2 . "/" . $this->image)) ? unlink($folder_tesseract_local2 . "/" . $this->image) : "";
        return;
    }
    /*BUILD EXECUTE PATH*/
    public function set_zbar_bin()
    {
        return $this->build["bin"] = "c:\\ZBar\\bin";
    }

    /*REMOVE UPFOLDERS AND SUBFOLDERS FOLDERS*/
    public function gerar_registro_arquivos()
    {
        if (!empty($this->dir_scan)) {
            if (count($this->dir_scan) > 4) {
                $removes = array(".", "..", "process", "results");
                for ($i = 0; $i < count($this->dir_scan); $i++) {
                    (!in_array($this->dir_scan[$i], $removes)) ? $this->files[] = $this->dir_scan[$i] : "";
                }
            }
        }
        return;
    }

    public function zbar_reader()
    {
        $this->set_zbar_bin();
        $read = $this->read_barcode();
        return $read;
    }

    public function read_barcode()
    {
        $read_zbar = $this->build["path"]["rede"]["results"] . "\\" . $this->build["images"]->from;
        if (file_exists($read_zbar)) {
            $ZbarDecoder = new RobbieP\ZbarQrdecoder\ZbarDecoder();
            $ZbarDecoder->setPath($this->build["bin"]);
            $zbar = $ZbarDecoder->make($read_zbar);
            return $this->reader->zbar = $zbar;
        }
        return;
    }

    /*FILES*/
    public function gerar_registro_files_escaneados()
    {
        return $this->files;
    }

    /*READER*/
    public function gerar_registro_reader()
    {
        return $this->build["reader"] = $this->reader;
    }

    /*CREATE PDF FILE*/
    public function create_pdf()
    {
        /*TEMP FILE PLACER*/
        $path_tmp = $this->build["path"]["local"]["process"];

        /*CAMINHO DO CERTIFICADO .crt*/
        $certificado_path = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]) . "/cert";

        /*SPLIT IMAGE NAME AND FORMAT*/
        $format_and_extensions = explode(".", $this->image);

        /*NOME DA EMPRESA*/
        $nomeEmpresa = explode(":", $this->build["crt"]["subject"]["CN"]);

        /*NOME DO ARQUIVO PDF*/
        $pdfname = $this->entry->cnpj . str_pad($this->entry->nfe, 9, "0", STR_PAD_LEFT);

        /*TCPDF*/
        $tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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
        //echo json_encode($path_tmp);
        $tcpdf->Image($path_tmp . "/" . $this->build["images"]->from, 2, 10, 200, 50, "jpeg", false);
        /*ACCESS CERTIFICADO .crt*/
        $certificate = 'file://' . $certificado_path . "/" . $this->entry->cnpj . ".crt";
        /*SET SIGNATURE*/
        $tcpdf->setSignature($certificate, $certificate, '', '', 2, '');
        $tcpdf->SetFont('helvetica', '', 5);
        // print a line of text
        $text1 = $nomeEmpresa[0];
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
        $tcpdf->Output($path_tmp . "/" . $pdfname . ".pdf", "F");
        /*PUT IN FOLDER Y/M/D*/
        if (file_exists($path_tmp . "/" . $pdfname . ".pdf")) {
            $server = file_get_contents($path_tmp . "/" . $pdfname . ".pdf");
            $path_rede_save_pdf = $this->build["path_rede"] . "scanner\\" . date('Y') . "\\" . date('m') . "\\" . date('d');
            (!is_dir($path_rede_save_pdf)) ? mkdir($path_rede_save_pdf, 0777, true) : "";
            (!file_exists($path_rede_save_pdf . "\\" . $pdfname . ".pdf")) ? file_put_contents($path_rede_save_pdf . "\\" . $pdfname . ".pdf", "") : "";
            file_put_contents($path_rede_save_pdf . "\\" . $pdfname . ".pdf", $server);
        }
        return;
    }

    /*LISTAR CERTIFICADOS VENCIDOS*/
    public function factory_expired_digitals_signs()
    {
        $nArray = array();
        $certificado_path = $this->build["path_local"] . str_replace("./", "", $this->build["path_exec"]);
        for ($i = 0; $i < count($this->entry); $i++) {
            if (file_exists($certificado_path . "cert/" . $this->entry[$i])) {
                $certificado_expirado = file_get_contents($certificado_path . "cert/" . $this->entry[$i]);
                openssl_pkcs12_read($certificado_expirado, $certificado, base64_decode(CERTIFICADOS));
                $certPriv = openssl_x509_parse(openssl_x509_read($certificado['cert']));
                $data_expired = date('d/m/Y', $certPriv['validTo_time_t']);
                $data_expired_xplode = array_reverse(explode("/", $data_expired));
                /*STD CLASS*/
                $this->validar_vencimentos($data_expired_xplode, $certPriv);
                $this->certificated->cnpj = str_replace(".pfx", "", $this->entry[$i]);
                $this->certificated->empresa = explode(":", $certPriv["subject"]["CN"])[0];
                $nArray[$i] = $this->certificated;
            }
        }
        return $nArray;
    }

    public function validar_vencimentos($vencimento, $data)
    {
        $this->certificated = new stdClass();
        /*VENCIMENTOS*/
        if (intval($vencimento[0]) < date('Y')) {
            $this->certificated->timer = "Certificado Expirado!";
            $this->certificated->expire = implode("/", array_reverse($vencimento));
            $this->certificated->dias_p_expirar = 0;
        } else if (intval($vencimento[1]) < date('m')) {
            $this->certificated->timer = "Este Certificado vence este Mês!";
            $this->certificated->expire = implode("/", array_reverse($vencimento));
            $this->certificated->dias_p_expirar = 30;
        } else if (intval($vencimento[2]) < date('d')) {
            $total_de_dias_do_mes_atual = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
            $total_dias_corridos = intval($total_de_dias_do_mes_atual) - intval(date('d'));
            $total_dias_para_expirar = intval($total_dias_corridos) + intval($vencimento[2]);
            $this->certificated->timer = "Este certificado expira em " . $total_dias_para_expirar . " dias.";
            $this->certificated->expire = implode("/", array_reverse($vencimento));
            $this->certificated->dias_p_expirar = $total_dias_para_expirar;
        }
        return;
    }
}
