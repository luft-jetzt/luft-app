<?php declare(strict_types=1);

namespace App\Air\AirQuality\Calculator;

use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;

abstract class AbstractAirQualityCalculator implements AirQualityCalculatorInterface
{
    /** @var array $pollutionLevels */
    protected $pollutionLevels = [];

    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface
    {
        $this->pollutionLevels[$pollutionLevel->getPollutionIdentifier()] = $pollutionLevel;

        return $this;
    }

    public function getPollutionLevels(): array
    {
        return $this->pollutionLevels;
    }
}
