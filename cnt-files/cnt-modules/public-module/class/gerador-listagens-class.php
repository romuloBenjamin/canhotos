<?php
class Gerador_listagens
{
    var $listas;
    var $folder;
    var $module;
    var $entry;
    var $swit;
    var $paths;
    var $build;

    public function __construct()
    {
        $this->build = array();
    }

    /*GERAR LISTAS*/
    public function compound_listas()
    {
        $gerador = new Gerador_listagens();
        switch ($this->listas) {
            case 'listar-departamentos':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-cargos':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-filiais':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-equipes':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-lideres':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-generos':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-periodos':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-ramais-batua':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-ramais-hasegawa':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-aniversariantes':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-status':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-regioes-transportadas':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-transportadores':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
            case 'listar-motivos-retorno-nfe':
                $gerador->listas = $this->listas;
                $gerador->swit = $this->module;
                $resultSet = $gerador->create_json_list();
                break;
                /*default:break;*/
        }
        return $resultSet;
    }

    /*CONFIRMA SE EXISTE LISTAGEM*/
    public function exist_listas()
    {
        $createListagem = new Gerador_listagens();
        $createListagem->build = $this->build;
        if (!file_exists($this->paths)) {
            $createListagem->paths = $this->paths;
            $resultSet = $createListagem->create_empty_listas();
        } else {
            $resultSet = null;
        }
        return $resultSet;
    }

    /*CREATE EMPTY LISTAS*/
    public function create_empty_listas()
    {
        return file_put_contents($this->paths, "");
    }

    /*CREATE LISTAS*/
    public function populate_listas()
    {
        $placeJsons = "{\"setdata\":[{\"date\": \"" . $this->build["last-date-update"] . "\"}, {\"hour\": \"" . $this->build["last-hour-update"] . "\"}], \"dataset\": [" . $this->build["loopdata"] . "]}";
        if (empty(file_get_contents($this->paths))) {
            $resultSet = file_put_contents($this->paths, $placeJsons);
        } else {
            $confirmarData = json_decode(file_get_contents($this->paths));
            $last_uppdate = strtotime($confirmarData->setdata[0]->date);
            if ($last_uppdate === strtotime(date('Y-m-d'))) {
                $resultSet = null;
            } else {
                if ($last_uppdate < strtotime(date('Y-m-d'))) {
                    $resultSet = file_put_contents($this->paths, $placeJsons);
                } else {
                    $resultSet = null;
                }
            }
        }
        return $resultSet;
    }

    /*CREATE JSON LISTA*/
    public function create_json_list()
    {
        /*GERADOR DE LISTAGENS*/
        $nListagem = new Gerador_listagens();
        /*CALL MODULE CLASS*/
        switch ($this->swit) {
            case 'usuarios':
                $newModule = new Usuarios_module();
                break;
            case 'logistica':
                $newModule = new Logistica_compound();
                break;
                /*default:break; */
        }
        $newModule->swit = $this->listas;
        if ($this->swit == "usuarios") $nListagem->build["loopdata"] = $newModule->compound_usuarios();
        if ($this->swit == "logistica") $nListagem->build["loopdata"] = $newModule->compound_logistica();
        $nListagem->paths = DIR_PATH . "cnt-modules/" . $this->swit . "-module/jsons/" . $this->listas . "-json.json";
        $nListagem->build["last-date-update"] = date('Y-m-d');
        $nListagem->build["last-hour-update"] = date('H:i:s');
        ///*CRIA LISTA SE NÃO EXISTE*/
        $nListagem->exist_listas();
        $nListagem->populate_listas();
    }
}
