<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class PM10 extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'mg/m<sup>3</sup>';
    }

    public function getName(): string
    {
        return 'Feinstaub PM10';
    }

    public function getPollutionLevel(): PollutionLevel
    {
        return new PollutionLevel(10, 20, 30, 40);
    }
}