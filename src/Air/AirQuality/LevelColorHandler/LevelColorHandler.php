<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorHandler;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\AirQuality\LevelColorCollection\LevelColorCollectionInterface;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\ViewModel\MeasurementViewModel;

class LevelColorHandler implements LevelColorHandlerInterface
{
    /** @var AirQualityCalculatorInterface $airQualityCalculator */
    protected $airQualityCalculator;

    /** @var LevelColorCollectionInterface $levelColorCollection */
    protected $levelColorCollection;

    public function __construct(AirQualityCalculatorInterface $airQualityCalculator, LevelColorCollectionInterface $levelColorCollection)
    {
        $this->airQualityCalculator = $airQualityCalculator;
        $this->levelColorCollection = $levelColorCollection;
    }

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

    public function pollutionColor(int $pollutionLevel): string
    {
        return $this->levelColorCollection->getBackgroundColor($pollutionLevel);
    }

    public function pollutionColorName(int $pollutionLevel): string
    {
        return $this->levelColorCollection->getBackgroundColorName($pollutionLevel);
    }

    public function getLevelsForMeasurement(string $pollutantIdentifier): PollutionLevelInterface
    {
        $pollutionLevels = $this->airQualityCalculator->getPollutionLevels();

        return $pollutionLevels[$pollutantIdentifier];
    }
}
