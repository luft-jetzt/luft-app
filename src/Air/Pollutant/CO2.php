<?php declare(strict_types=1);

namespace App\Air\Pollutant;

class CO2 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'ppm';
        $this->unitPlain = 'ppm';
        $this->name = 'Kohlenstoffdioxid';
        $this->shortNameHtml = 'CO<sub>2</sub>';
        $this->showOnMap = false;
        $this->includeInTweets = false;
        $this->decimals = 2;
    }
}
