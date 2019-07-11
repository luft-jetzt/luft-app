<?php declare(strict_types=1);

namespace App\Air\AirQuality\Calculator;

use App\Air\ViewModel\MeasurementViewModel;
use App\Air\AirQuality\LevelCalculator\LevelCalculator;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;

class AirQualityCalculator extends AbstractAirQualityCalculator
{
    public function calculatePollutantList(array $pollutantList): int
    {
        $maxLevel = 1;

        /** @var array $pollutant */
        foreach ($pollutantList as $pollutant) {
            /** @var MeasurementViewModel $measurementViewModel */
            foreach ($pollutant as $measurementViewModel) {
                $level = $this->calculateViewModel($measurementViewModel);

                if ($level > $maxLevel) {
                    $maxLevel = $level;
                }
            }
        }

        return $maxLevel;
    }

    public function calculateViewModel(MeasurementViewModel $measurementViewModel): int
    {
        /** @var PollutionLevelInterface $level */
        $level = $this->pollutionLevels[$measurementViewModel->getMeasurement()->getIdentifier()];

        $levelValue = LevelCalculator::calculate($level, $measurementViewModel->getData());

        $measurementViewModel->setPollutionLevel($levelValue);

        return $levelValue;
    }
}
