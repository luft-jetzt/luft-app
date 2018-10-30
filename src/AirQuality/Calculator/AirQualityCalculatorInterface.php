<?php declare(strict_types=1);

namespace App\AirQuality\Calculator;

use App\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Pollution\Box\Box;

interface AirQualityCalculatorInterface
{
    public function calculateBoxList(array $boxList): int;
    public function calculateBox(Box $box): int;
    public function addPollutionLevel(PollutionLevelInterface $pollutionLevel): AirQualityCalculatorInterface;
}
