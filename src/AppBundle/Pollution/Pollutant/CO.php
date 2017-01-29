<?php

namespace AppBundle\Pollution\Pollutant;

class CO extends AbstractPollutant
{
    public function getUnit(): string
    {
        return 'mg/m3';
    }

    public function getName(): string
    {
        return 'Kohlenmonoxid';
    }
}