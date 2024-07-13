<?php declare(strict_types=1);

namespace App\Air\AirQuality\Calculator;

use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;

abstract class AbstractAirQualityCalculator implements AirQualityCalculatorInterface
{
    protected array $pollutionLevels = [];

    #[\Override]
    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface
    {
        $this->pollutionLevels[$pollutionLevel->getPollutionIdentifier()] = $pollutionLevel;

        return $this;
    }

    #[\Override]
    public function getPollutionLevels(): array
    {
        return $this->pollutionLevels;
    }
}
