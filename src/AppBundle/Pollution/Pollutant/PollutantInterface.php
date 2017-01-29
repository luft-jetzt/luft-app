<?php

namespace AppBundle\Pollution\Pollutant;

interface PollutantInterface
{
    public function getUnit(): string;
    public function getName(): string;
}