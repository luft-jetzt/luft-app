<?php declare(strict_types=1);

namespace App\AirQuality\Calculator;

use App\AirQuality\PollutionLevel\PollutionLevelInterface;

abstract class AbstractAirQualityCalculator implements AirQualityCalculatorInterface
{
    protected $pollutionLevels = [];

    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface
    {
        $this->pollutionLevels[$pollutionLevel->getPollutionIdentifier()] = $pollutionLevel;

        return $this;
    }
}
