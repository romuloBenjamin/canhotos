<?php
class Pesquisar_patterns
{
    public function patterns_compound()
    {
        $lista_keys = new Pesquisar_patterns();
        $lista_keys->swit = $this->swit;
        switch ($this->swit) {
            case 'pesquisar-romaneios':
                $lista_keys->entry = "ids,cadastro-data,cadastro-hora,transportador,qtd,dados-nfe,dados-cli,data-saida,romaneio,setor,diaria";
                $resultSet = $lista_keys->patterns_build();
                break;
                /*default:break; */
        }
        return $resultSet;
    }
    /*BUILD PATTERNS*/
    public function patterns_build()
    {
        return explode(",", $this->entry);
    }
}
