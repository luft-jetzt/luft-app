<?php declare(strict_types=1);

namespace App\Air\Measurement;

class O3 extends AbstractMeasurement
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Ozon';
        $this->shortNameHtml = 'O<sub>3</sub>';
        $this->showOnMap = true;
        $this->includeInTweets = true;
    }
}
