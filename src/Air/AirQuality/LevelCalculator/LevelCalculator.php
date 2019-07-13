<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelCalculator;

use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Entity\Data;

class LevelCalculator
{
    private function __construct()
    {

    }

    public static function calculate(PollutionLevelInterface $pollutionLevel, Data $data, bool $plusOne = true): int
    {
        $levels = array_reverse($pollutionLevel->getLevels(), true);

        $current = null;

        foreach ($levels as $level => $value) {
            if (!$current || $data->getValue() < $value) {
                $current = $level;
            }
        }

        // as the index for level 2 is 1, we have to +1
        return $current + ($plusOne ? 1 : 0);
    }
}