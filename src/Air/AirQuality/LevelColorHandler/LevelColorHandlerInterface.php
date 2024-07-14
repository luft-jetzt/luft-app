<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorHandler;

use App\Air\AirQuality\PollutionLevel\PollutionLevelInterface;
use App\Air\ViewModel\PollutantViewModel;

interface LevelColorHandlerInterface
{
    public function maxPollutionLevel(array $pollutionList): int;
    public function maxPollutionColorName(array $pollutionList): string;
    public function pollutionColor(PollutantViewModel $pollutantViewModel): string;
    public function pollutionColorName(PollutantViewModel $pollutantViewModel): string;
    public function getLevelsForPollutant(string $pollutantIdentifier): PollutionLevelInterface;
}
