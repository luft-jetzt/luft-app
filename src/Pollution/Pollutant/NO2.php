<?php declare(strict_types=1);

namespace App\Pollution\Pollutant;

/**
 * @deprecated
 */
class NO2 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Stickstoffdioxid';
        $this->shortNameHtml = 'NO<sub>2</sub>';
    }
}
