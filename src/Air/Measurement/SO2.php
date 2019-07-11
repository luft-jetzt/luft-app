<?php declare(strict_types=1);

namespace App\Air\Measurement;

class SO2 extends AbstractMeasurement
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Schwefeldioxid';
        $this->shortNameHtml = 'SO<sub>2</sub>';
        $this->showOnMap = true;
    }
}
