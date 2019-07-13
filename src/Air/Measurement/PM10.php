<?php declare(strict_types=1);

namespace App\Air\Measurement;

class PM10 extends AbstractMeasurement
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Feinstaub PM10';
        $this->shortNameHtml = 'PM<sub>10</sub>';
        $this->showOnMap = true;
        $this->includeInTweets = true;
    }
}
