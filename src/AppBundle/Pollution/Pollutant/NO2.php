<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class NO2 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Stickstoffdioxid';
        $this->pollutionLevel = new PollutionLevel(40, 80, 150, 250);
    }
}
