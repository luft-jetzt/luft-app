<?php

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class O3 extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'µg/m<sup>3</sup>';
    }

    public function getName(): string
    {
        return 'Ozon';
    }

    public function getPollutionLevel(): PollutionLevel
    {
        return new PollutionLevel(10, 20, 30, 40);
    }
}