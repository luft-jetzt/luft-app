<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class SO2 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unit = 'µg/m<sup>3</sup>';
        $this->name = 'Schwefeldioxid';
        $this->pollutionLevel = new PollutionLevel(105, 210, 350, 600);
    }
}