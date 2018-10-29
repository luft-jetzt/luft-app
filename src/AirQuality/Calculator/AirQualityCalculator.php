<?php declare(strict_types=1);

namespace App\AirQuality\Calculator;

use App\Pollution\Box\Box;

class AirQualityCalculator extends AbstractAirQualityCalculator
{
    public function calculate(array $boxList): int
    {
        /** @var Box $box */
        foreach ($boxList as $box) {
            $box->getPollutant();
        }
    }

}
