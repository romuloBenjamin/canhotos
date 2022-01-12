<?php
class Paginations
{
    var $entry;
    var $swit;
    var $build;

    public function __construct()
    {
        $this->build = array();
    }

    /*COMPOUND*/
    public function paginations_compound()
    {
        $compound = new Paginations();
        switch ($this->swit) {
            case 'listar-retronos-nfe-paginations':
                $compound->entry = json_decode($this->entry);
                $compound->swit = $this->swit;
                $this->build["origin"] = json_decode($this->entry);
                $factory = $compound->factory_paginations();
                $compound->entry = $factory["paginations"];
                $compound->build = $this->build;
                $maxRegistros = $compound->loopdata_paginations();
                $compound->entry = $maxRegistros;
                $compound->build = $factory;
                $compound->swit = "place-max-registros";
                $resultSet = $compound->factory_paginations();
                break;
                /*default:break;*/
        }
        return json_encode($resultSet);
    }
    /*FACTORY*/
    public function factory_paginations()
    {
        $factory = new Paginations_factory();
        $factory->entry = $this->entry;
        $factory->swit = $this->swit;
        $factory->build = $this->build;
        return $factory->factory_paginations();
    }
    /*LOOPDATA*/
    public function loopdata_paginations()
    {
        $loops = new Paginations_loopdata();
        $loops->entry = $this->entry;
        $loops->swit = $this->swit;
        $loops->build = $this->build;
        return $loops->loopdata_paginations();
    }
}
