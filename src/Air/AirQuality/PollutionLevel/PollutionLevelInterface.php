<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

use App\Entity\Data;

interface PollutionLevelInterface
{
    public function getLevels(): array;
    public function getPollutionIdentifier(): string;
}
