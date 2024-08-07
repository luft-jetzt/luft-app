<?php declare(strict_types=1);

namespace App\Air\Pollutant;

class NO2 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Stickstoffdioxid';
        $this->shortNameHtml = 'NO<sub>2</sub>';
        $this->showOnMap = true;
        $this->includeInTweets = true;
        $this->decimals = 0;
    }
}
