<?php declare(strict_types=1);

namespace App\Pollution\Pollutant;

use App\Pollution\PollutionLevel\PollutionLevel;

class SO2 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Schwefeldioxid';
        $this->pollutionLevel = new PollutionLevel(105, 210, 350, 600);
    }
}
