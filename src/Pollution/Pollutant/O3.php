<?php declare(strict_types=1);

namespace App\Pollution\Pollutant;

/**
 * @deprecated
 */
class O3 extends AbstractPollutant
{
    public function __construct()
    {
        $this->unitHtml = 'µg/m<sup>3</sup>';
        $this->unitPlain = 'µg/m³';
        $this->name = 'Ozon';
        $this->shortNameHtml = 'O<sub>3</sub>';
    }
}
