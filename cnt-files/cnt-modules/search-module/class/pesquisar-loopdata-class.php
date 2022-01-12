<?php
class Pesquisar_loopdata
{
    var $entry;
    var $swit;
    var $build;

    public function __construct()
    {
        $this->build = array();
    }
    /*COMPOUND*/
    public function loopdata_compound()
    {
        $loops = new Pesquisar_loopdata();
        $loops->entry = $this->entry;
        switch ($this->swit) {
            case 'pesquisar-romaneios':
                $loops->swit = $this->swit;
                $sql = $loops->loopdata_sql_listar_retornos();
                $loops->entry = $sql;
                $results = $loops->loopdata_execute();
                $loops->entry = $results;
                $loops->build = $this->build;
                /*SEM DADOS NO LOOPDATA*/
                if ($results->num_rows == 0) return $loops->loopdata_no_data();
                /*LOOPDATA*/
                if ($results->num_rows != 0) return $loops->loopdata_build();
                break;
                /*default:break;*/
        }
    }
    /*SQL*/
    public function loopdata_sql_listar_retornos()
    {
        /*PATTERNS*/
        $patterns = json_decode($this->entry);
        /*SQL*/
        $sql = "SELECT * FROM uni_intra_retorno_nfes";
        $sql .= " WHERE ";
        $sql .= "uirn_romaneios LIKE '%" . trim($patterns->search) . "%'";
        return $sql;
    }
    /*EXECUTE*/
    public function loopdata_execute()
    {
        $exec = new Main();
        $exec->sql = $this->entry;
        return $exec->executeQuery();
    }
    /*BUILD -> data*/
    public function loopdata_build()
    {
        $patterns = $this->build["patterns"];
        $nArray = array();
        $array_combine = array();
        $array_combine["status"] = "1";
        $i = 0;
        while ($items = $this->entry->fetch_array()) {
            foreach ($items as $tags => $content) {
                if (!is_int($tags)) {
                    $nArray[$tags] = $content;
                }
            }
            $array_combine["data"][$i] = array_combine($patterns, $nArray);
            $array_combine["data"][$i]["dados-cli"] = unserialize($array_combine["data"][$i]["dados-cli"]);
            $array_combine["data"][$i]["dados-nfe"] = unserialize($array_combine["data"][$i]["dados-nfe"]);
            $i++;
        }
        $array_combine["msn"] = "Sucesso! -> " . $this->swit;
        return json_encode($array_combine);
    }
    /*BUILD -> no data*/
    public function loopdata_no_data()
    {
        return json_encode(array("status" => "0", "msn" => "Desculpe! Não localizamos o Romaneio"));
    }
}
