<?php declare(strict_types=1);

namespace App\Air\AirQuality\Calculator;

use App\Air\ViewModel\PollutantViewModel;
use App\Air\AirQuality\LevelCalculator\LevelCalculator;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;

class AirQualityCalculator extends AbstractAirQualityCalculator
{
    #[\Override]
    public function calculatePollutantList(array $pollutantList): int
    {
        $maxLevel = 1;

        /** @var array $pollutant */
        foreach ($pollutantList as $pollutant) {
            /** @var PollutantViewModel $pollutantViewModel */
            foreach ($pollutant as $pollutantViewModel) {
                $level = $this->calculateViewModel($pollutantViewModel);

                if ($level > $maxLevel) {
                    $maxLevel = $level;
                }
            }
        }

        return $maxLevel;
    }

    #[\Override]
    public function calculateViewModel(PollutantViewModel $pollutantViewModel): int
    {
        /** @var PollutionLevelInterface $level */
        $level = $this->pollutionLevels[$pollutantViewModel->getPollutant()->getIdentifier()];

        $levelValue = LevelCalculator::calculate($level, $pollutantViewModel->getData());

        $pollutantViewModel->setPollutionLevel($levelValue);

        return $levelValue;
    }
}
