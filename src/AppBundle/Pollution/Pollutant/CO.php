<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class CO extends AbstractPollutant
{
    public function __construct()
    {
        $this->unit = 'µg/m<sup>3</sup>';
        $this->name = 'Kohlenmonoxid';
        $this->pollutionLevel = new PollutionLevel(3000, 7000, 10000, 15000);
    }
}