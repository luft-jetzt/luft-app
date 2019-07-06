<?php declare(strict_types=1);

namespace App\Air\AirQuality\Calculator;

use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Pollution\Box\Box;

interface AirQualityCalculatorInterface
{
    public function calculatePollutantList(array $pollutantList): int;
    public function calculateBox(Box $box): int;
    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface;
    public function getPollutionLevels(): array;
}
