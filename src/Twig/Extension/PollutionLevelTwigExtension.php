<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Air\ViewModel\MeasurementViewModel;
use App\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\AirQuality\PollutionLevel\PollutionLevelInterface;

class PollutionLevelTwigExtension extends \Twig_Extension
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

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('max_pollution_level', [$this, 'maxPollutionLevel'], ['is_safe' => ['raw']]),
            new \Twig_SimpleFunction('pollution_color', [$this, 'pollutionColor'], ['is_safe' => ['raw']]),
            new \Twig_SimpleFunction('pollution_color_name', [$this, 'pollutionColorName'], ['is_safe' => ['raw']]),
            new \Twig_SimpleFunction('pollution_levels', [$this, 'getLevelsForPollutant'], ['is_safe' => ['raw']]),
        ];
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

    public function getLevelsForPollutant(string $pollutantIdentifier): PollutionLevelInterface
    {
        $pollutionLevels = $this->airQualityCalculator->getPollutionLevels();

        return $pollutionLevels[$pollutantIdentifier];
    }

    public function getName(): string
    {
        return 'pollution_level_extension';
    }
}

