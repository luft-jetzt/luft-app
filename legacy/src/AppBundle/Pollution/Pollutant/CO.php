<?php declare(strict_types=1);

namespace AppBundle\Pollution\Pollutant;

use AppBundle\Pollution\PollutionLevel\PollutionLevel;

class CO extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Kohlenmonoxid';
        $this->pollutionLevel = new PollutionLevel(3000, 7000, 10000, 15000);
    }
}