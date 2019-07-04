<?php declare(strict_types=1);

namespace App\AirQuality\Calculator;

use App\Air\ViewModel\MeasurementViewModel;
use App\AirQuality\PollutionLevel\PollutionLevelInterface;

interface AirQualityCalculatorInterface
{
    public function calculatePollutantList(array $pollutantList): int;
    public function calculateViewModel(MeasurementViewModel $measurementViewModel): int;
    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface;
    public function getPollutionLevels(): array;
}
