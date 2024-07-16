<?php declare(strict_types=1);

namespace App\Air\Pollutant;

class Temperature extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = '°C';
        $this->unitPlain = '°C';
        $this->name = 'Temperatur';
        $this->shortNameHtml = 'Temperatur';
        $this->showOnMap = false;
        $this->includeInTweets = false;
        $this->decimals = 0;
    }
}
