<?php declare(strict_types=1);

namespace App\Pollution\Pollutant;

use App\Pollution\PollutionLevel\PollutionLevel;

class O3 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Ozon';
        $this->pollutionLevel = new PollutionLevel(54, 108, 180, 240);
    }
}
