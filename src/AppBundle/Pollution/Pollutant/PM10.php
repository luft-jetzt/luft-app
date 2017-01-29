<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class PM10 extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'µg/<sup>3</sup>';
    }

    public function getName(): string
    {
        return 'Feinstaub PM10';
    }

    public function getPollutionLevel(): PollutionLevel
    {
        return new PollutionLevel(20, 35, 45, 75);
    }
}