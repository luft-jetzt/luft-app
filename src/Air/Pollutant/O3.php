<?php declare(strict_types=1);

namespace App\Air\Pollutant;

class O3 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Ozon';
        $this->shortNameHtml = 'O<sub>3</sub>';
        $this->showOnMap = true;
        $this->includeInTweets = true;
        $this->decimals = 0;
    }
}
