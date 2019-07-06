<?php declare(strict_types=1);

namespace App\Air\AirQuality\Calculator;

use App\Air\AirQuality\LevelCalculator\LevelCalculator;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Pollution\Box\Box;

class AirQualityCalculator extends AbstractAirQualityCalculator
{
    public function calculatePollutantList(array $pollutantList): int
    {
        $maxLevel = 1;

        /** @var array $pollutant */
        foreach ($pollutantList as $pollutant) {
            /** @var Box $box */
            foreach ($pollutant as $box) {
                $level = $this->calculateBox($box);

                if ($level > $maxLevel) {
                    $maxLevel = $level;
                }
            }
        }

        return $maxLevel;
    }

    public function calculateBox(Box $box): int
    {
        /** @var PollutionLevelInterface $level */
        $level = $this->pollutionLevels[$box->getPollutant()->getIdentifier()];

        $levelValue = LevelCalculator::calculate($level, $box->getData());

        $box->setPollutionLevel($levelValue);

        return $levelValue;
    }
}
