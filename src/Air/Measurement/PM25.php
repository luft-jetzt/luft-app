<?php declare(strict_types=1);

namespace App\Air\Measurement;

class PM25 extends AbstractMeasurement
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Feinstaub PM25';
        $this->shortNameHtml = 'PM<sub>25</sub>';
    }
}
