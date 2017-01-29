<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class NO2 extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'µg/m<sup>3</sup>';
    }

    public function getName(): string
    {
        return 'Stickstoffdioxid';
    }

    public function getPollutionLevel(): PollutionLevel
    {
        return new PollutionLevel(60, 120, 200, 260);
    }
}