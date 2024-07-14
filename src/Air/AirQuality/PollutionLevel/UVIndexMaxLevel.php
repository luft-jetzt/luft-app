<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class UVIndexMaxLevel extends UVIndexLevel
{
    #[\Override]
    public function getPollutionIdentifier(): string
    {
        return 'uvindex_max';
    }
}
