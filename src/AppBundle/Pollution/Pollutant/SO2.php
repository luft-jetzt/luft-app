<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class SO2 extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'µg/m<sup>3</sup>';
    }

    public function getName(): string
    {
        return 'Schwefeldioxid';
    }

    public function getPollutionLevel(): PollutionLevel
    {
        return new PollutionLevel(105, 210, 350, 600);
    }
}