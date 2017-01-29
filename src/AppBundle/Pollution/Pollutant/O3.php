<?php

namespace AppBundle\Pollution\Pollutant;

class O3 extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'mg/m3';
    }

    public function getName(): string
    {
        return 'Ozon';
    }
}