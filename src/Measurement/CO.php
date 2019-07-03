<?php declare(strict_types=1);

namespace App\Measurement;

class CO extends AbstractMeasurement
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Kohlenmonoxid';
        $this->shortNameHtml = 'CO';
    }
}
