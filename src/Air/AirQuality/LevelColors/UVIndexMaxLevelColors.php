<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class UVIndexMaxLevelColors extends UVIndexLevelColors
{
    public function getIdentifier(): string
    {
        return 'uvindex_max';
    }
}
