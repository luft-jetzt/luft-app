<?php declare(strict_types=1);

namespace App\AirQuality\Calculator;

use App\AirQuality\PollutionLevel\PollutionLevelInterface;

interface AirQualityCalculatorInterface
{
    public function calculate(array $boxList): int;
    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface;
}
