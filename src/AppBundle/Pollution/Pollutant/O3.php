<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class O3 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unit = 'µg/m<sup>3</sup>';
        $this->name = 'Ozon';
        $this->pollutionLevel = new PollutionLevel(54, 108, 180, 240);
    }
}