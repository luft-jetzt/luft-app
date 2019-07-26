<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorHandler;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\ViewModel\MeasurementViewModel;

class LevelColorHandler implements LevelColorHandlerInterface
{
    protected $backgroundColors = [
        0 => 'white',
        1 => '#28a745',
        2 => '#28a745',
        3 => '#ffc107',
        4 => '#ffc107',
        5 => '#dc3545',
        6 => '#dc3545',
    ];

    protected $backgroundColorNames = [
        0 => 'white',
        1 => 'green',
        2 => 'green',
        3 => 'yellow',
        4 => 'yellow',
        5 => 'red',
        6 => 'red',
    ];

    /** @var AirQualityCalculatorInterface $airQualityCalculator */
    protected $airQualityCalculator;

    public function __construct(AirQualityCalculatorInterface $airQualityCalculator)
    {
        $this->airQualityCalculator = $airQualityCalculator;
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
        return $this->backgroundColors[$pollutionLevel];
    }

    public function pollutionColorName(int $pollutionLevel): string
    {
        return $this->backgroundColorNames[$pollutionLevel];
    }

    public function getLevelsForMeasurement(string $pollutantIdentifier): PollutionLevelInterface
    {
        $pollutionLevels = $this->airQualityCalculator->getPollutionLevels();

        return $pollutionLevels[$pollutantIdentifier];
    }
}
