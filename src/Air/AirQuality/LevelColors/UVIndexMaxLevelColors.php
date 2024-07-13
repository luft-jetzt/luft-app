<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class UVIndexMaxLevelColors extends UVIndexLevelColors
{
    #[\Override]
    public function getIdentifier(): string
    {
        return 'uvindex_max';
    }
}
