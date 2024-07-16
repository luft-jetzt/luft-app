<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorHandler;

use App\Air\AirQuality\Calculator\AirQualityCalculatorInterface;
use App\Air\AirQuality\LevelColorCollection\LevelColorCollectionInterface;
use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\ViewModel\PollutantViewModel;

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
            /** @var PollutantViewModel $pollutantViewModel */
            foreach ($pollutant as $pollutantViewModel) {
                if ($maxLevel < $pollutantViewModel->getPollutionLevel()) {
                    $maxLevel = $pollutantViewModel->getPollutionLevel();
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
    public function pollutionColor(PollutantViewModel $pollutantViewModel): string
    {
        return $this->levelColorCollection->getLevelColorsForPollutant($pollutantViewModel->getPollutant()->getIdentifier())->getBackgroundColors()[$pollutantViewModel->getPollutionLevel()];
    }

    #[\Override]
    public function pollutionColorName(PollutantViewModel $pollutantViewModel): string
    {
        return $this->levelColorCollection->getLevelColorsForPollutant($pollutantViewModel->getPollutant()->getIdentifier())->getBackgroundColorNames()[$pollutantViewModel->getPollutionLevel()];
    }

    #[\Override]
    public function getLevelsForPollutant(string $pollutantIdentifier): PollutionLevelInterface
    {
        $pollutionLevels = $this->airQualityCalculator->getPollutionLevels();

        return $pollutionLevels[$pollutantIdentifier];
    }
}
