<?php declare(strict_types=1);

namespace App\Air\Pollutant;

class PM25 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Feinstaub PM25';
        $this->shortNameHtml = 'PM<sub>25</sub>';
        $this->showOnMap = true;
        $this->includeInTweets = true;
        $this->decimals = 0;
    }
}
