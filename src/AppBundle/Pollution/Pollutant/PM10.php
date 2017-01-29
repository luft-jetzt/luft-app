<?php

namespace AppBundle\Pollution\Pollutant;

class PM10 extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'mg/m3';
    }

    public function getName(): string
    {
        return 'Feinstaub PM10';
    }
}