<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorHandler;

use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;

interface LevelColorHandlerInterface
{
    public function maxPollutionLevel(array $pollutionList): int;
    public function pollutionColor(int $pollutionLevel): string;
    public function pollutionColorName(int $pollutionLevel): string;
    public function getLevelsForMeasurement(string $pollutantIdentifier): PollutionLevelInterface;
}
