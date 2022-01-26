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
        $this->build["path_process"] = "./scanner/process";
        $this->build["path_results"] = "./scanner/results";
        $this->build["path_exec"] = "./cnt-files/cnt-assets/";
    }

    /*COMPOUND build to SCANNER*/
    public function compound_build()
    {
        $compound = new Scanner_compound();
        $compound->build = array_merge(json_decode($this->entry, true), $this->build);
        return $this->build = $compound->build;
    }

    /*LISTAR CERTIFICADOS VENCIDOS*/
    public function listar_certificados_vencidos()
    {
        $certificados = $this->listar_certificados();
        return json_encode($certificados);
    }

    /*LISTAR CERTIFICADOS*/
    public function listar_certificados()
    {
        $factory = new Scanner_factory();
        $factory->entry = $this->entry;
        $factory->build = $this->build;
        $certificados = $factory->factory_expired_digitals_signs();
        return $certificados;
    }

    /*COMPOUND AMBIENT DE PROCESSAMENTO*/
    public function compound_ambient_patterns()
    {
        $patterns = new Scanner_patterns();
        $patterns->build = $this->build;
        $patterns->patterns_ambient();
        $patterns->patterns_ambient_usuario();
        $patterns->patterns_ambient_process();
        $patterns->patterns_ambient_results();
        $this->build = $patterns->update_build_path();
        return;
    }

    /*COMPOUND BUILD PRE PATH*/
    public function compound_build_prepath()
    {
        $patterns = new Scanner_patterns();
        $patterns->build = $this->build;
        $this->build = $patterns->update_build_path();
        return;
    }

    /*COMPOUND ESCANEAR ARQUIVOS PARA GERAÇÃO DE LISTA*/
    public function compound_lista_canhotos()
    {
        $scann = new Scanner_factory();
        $scann->entry = $this->entry;
        $scann->build = $this->build;
        $scann->scanear_pasta_emrede();
        $scann->gerar_registro_arquivos();
        $files = $scann->gerar_registro_files_escaneados();
        if (!empty($files)) return $files;
        if (empty($files)) return;
    }

    /*CRIADOR DE SAMPLES*/
    public function compound_file_process2()
    {
        $this->compound_build_prepath();
        $this->create_sample_inprocess();
        $this->create_sample_inresults();
        return;
    }

    /*LER ZBAR*/
    public function reader_zbar()
    {
        $this->compound_build_prepath();
        $zbar = $this->compound_read_zbar();
        return $zbar;
    }

    /*SAVE TESSERACT FILES*/
    public function save_tesseract_files()
    {
        $this->entry = $this->entry["save"];
        $this->build_loop_save_tesseract();
        return;
    }
    public function build_loop_save_tesseract()
    {
        $identify = new Scanner_image_factory();
        $identify->build = $this->build;
        for ($i = 0; $i < count($this->entry); $i++) {
            $data = $this->entry[$i];
            $identify->image = $data;
            $identify->save_tesseract_loop();
        }
        return;
    }

    /*LER TESSERACT*/
    public function reader_tesseract()
    {
        $this->compound_build_prepath();
        return $this->compound_read_tesseract();
    }

    /*CREATE SAMPLE IN PROCESS*/
    public function create_sample_inprocess()
    {
        $images = new Scanner_image_factory();
        $images->image = $this->entry;
        $images->build = $this->build;
        $images->where = "usuario";
        $images->create_sample_inprocess2();
        return;
    }

    /*CREATE SAMPLE TO ZBAR AND TESSERACT*/
    public function create_sample_inresults()
    {
        $images = new Scanner_image_factory();
        $images->image = $this->entry;
        $images->build = $this->build;
        $images->where = "process";
        $images->create_sample_inresults2();
        return;
    }

    /*PREPARAR LEITURA DO ZBAR*/
    public function compound_read_zbar()
    {
        $images = new Scanner_image_factory();
        $images->image = $this->entry;
        $images->build = $this->build;
        $images->where = "process";
        return $images->prepare_reader_zbar();
    }

    /*PREPARE LEITURA TESSERACT*/
    public function compound_read_tesseract()
    {
        $images = new Scanner_image_factory();
        $images->image = $this->entry;
        $images->build = $this->build;
        $images->where = "process";
        return $images->prepare_reader_tesseract();
    }
}
