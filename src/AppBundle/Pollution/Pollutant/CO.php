<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class CO extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'µg/m<sup>3</sup>';
    }

    public function getName(): string
    {
        return 'Kohlenmonoxid';
    }

    public function getPollutionLevel(): PollutionLevel
    {
        return new PollutionLevel(3000, 7000, 10000, 15000);
    }
}