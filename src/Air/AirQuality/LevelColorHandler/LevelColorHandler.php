<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorHandler;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\AirQuality\LevelColorCollection\LevelColorCollectionInterface;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\ViewModel\MeasurementViewModel;

class LevelColorHandler implements LevelColorHandlerInterface
{
    public function __construct(protected AirQualityCalculatorInterface $airQualityCalculator, protected LevelColorCollectionInterface $levelColorCollection)
    {
    }

    #[\Override]
    public function maxPollutionLevel(array $pollutionList): int
    {
        $maxLevel = 0;

        /** @var array $pollutant */
        foreach ($pollutionList as $pollutant) {
            /** @var MeasurementViewModel $measurementViewModel */
            foreach ($pollutant as $measurementViewModel) {
                if ($maxLevel < $measurementViewModel->getPollutionLevel()) {
                    $maxLevel = $measurementViewModel->getPollutionLevel();
                }
            }
        }

        return $maxLevel;
    }

    #[\Override]
    public function maxPollutionColorName(array $pollutionList): string
    {
        $maxLevel = $this->maxPollutionLevel($pollutionList);

        return $this->levelColorCollection->getLevelColorsList()['standard']->getBackgroundColorNames()[$maxLevel];
    }

    #[\Override]
    public function pollutionColor(MeasurementViewModel $measurementViewModel): string
    {
        return $this->levelColorCollection->getLevelColorsForMeasurement($measurementViewModel->getMeasurement()->getIdentifier())->getBackgroundColors()[$measurementViewModel->getPollutionLevel()];
    }

    #[\Override]
    public function pollutionColorName(MeasurementViewModel $measurementViewModel): string
    {
        return $this->levelColorCollection->getLevelColorsForMeasurement($measurementViewModel->getMeasurement()->getIdentifier())->getBackgroundColorNames()[$measurementViewModel->getPollutionLevel()];
    }

    #[\Override]
    public function getLevelsForMeasurement(string $pollutantIdentifier): PollutionLevelInterface
    {
        $pollutionLevels = $this->airQualityCalculator->getPollutionLevels();

        return $pollutionLevels[$pollutantIdentifier];
    }
}
