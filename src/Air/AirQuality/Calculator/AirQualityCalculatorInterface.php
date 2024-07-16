<?php declare(strict_types=1);

namespace App\Air\AirQuality\Calculator;

use App\Air\ViewModel\PollutantViewModel;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;

interface AirQualityCalculatorInterface
{
    public function calculatePollutantList(array $pollutantList): int;
    public function calculateViewModel(PollutantViewModel $pollutantViewModel): int;
    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface;
    public function getPollutionLevels(): array;
}
