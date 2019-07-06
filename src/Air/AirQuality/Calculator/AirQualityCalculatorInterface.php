<?php declare(strict_types=1);

namespace App\Air\AirQuality\Calculator;

use App\Air\ViewModel\MeasurementViewModel;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;

interface AirQualityCalculatorInterface
{
    public function calculatePollutantList(array $pollutantList): int;
    public function calculateViewModel(MeasurementViewModel $measurementViewModel): int;
    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface;
    public function getPollutionLevels(): array;
}
