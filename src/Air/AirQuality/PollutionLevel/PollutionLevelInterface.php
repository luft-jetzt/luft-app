<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

interface PollutionLevelInterface
{
    public function getLevels(): array;
    public function getPollutionIdentifier(): string;
}
