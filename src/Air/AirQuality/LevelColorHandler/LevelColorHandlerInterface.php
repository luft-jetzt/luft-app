<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorHandler;

use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\ViewModel\MeasurementViewModel;

interface LevelColorHandlerInterface
{
    public function maxPollutionLevel(array $pollutionList): int;
    public function maxPollutionColorName(array $pollutionList): string;
    public function pollutionColor(MeasurementViewModel $measurementViewModel): string;
    public function pollutionColorName(MeasurementViewModel $measurementViewModel): string;
    public function getLevelsForMeasurement(string $pollutantIdentifier): PollutionLevelInterface;
}
