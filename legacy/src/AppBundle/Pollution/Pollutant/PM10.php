<?php declare(strict_types=1);

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class PM10 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Feinstaub PM10';
        $this->pollutionLevel = new PollutionLevel(20, 35, 45, 75);
    }
}