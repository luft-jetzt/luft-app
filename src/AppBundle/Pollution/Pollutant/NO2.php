<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class NO2 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unit = 'µg/m<sup>3</sup>';
        $this->name = 'Stickstoffdioxid';
        $this->pollutionLevel = new PollutionLevel(60, 120, 200, 260);
    }
}