<?php
class Paginations_factory
{
    var $entry;
    var $swit;
    var $build;

    public function __construct()
    {
        $this->build = array();
    }

    /*FACTORY*/
    public function factory_paginations()
    {
        $factory = new Paginations_factory();
        switch ($this->swit) {
            case 'listar-retronos-nfe-paginations':
                $factory->entry = $this->entry;
                $this->build["paginations"] = $factory->create_obj_paginations();
                $factory->build = $this->build;
                return $factory->build;
                break;
            case 'place-max-registros':
                $factory->entry = $this->entry;
                $factory->build = $this->build;
                return $factory->place_max_paginations();
                break;
                /*default:break; */
        }
    }
    /*CREATE OBJ*/
    public function create_obj_paginations()
    {
        $nArray = array();
        $nArray["ini"] = $this->entry->ini;
        $nArray["max"] = $this->entry->max;
        if (isset($this->entry->pages)) $nArray["pages"] = $this->entry->pages;
        if (!isset($this->entry->pages)) $nArray["pages"] = 1;
        $nArray["total_registros"] = null;
        if (isset($this->entry->search)) $nArray["search_mode"] = true;
        if (!isset($this->entry->search)) $nArray["search_mode"] = false;
        return json_decode(json_encode($nArray));
    }
    /*PLACE MAX PAGINATIONS*/
    public function place_max_paginations()
    {
        $patterns = $this->build["paginations"];
        $patterns->total_registros = $this->entry;
        return $patterns;
    }
}
