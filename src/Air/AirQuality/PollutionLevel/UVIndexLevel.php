<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class UVIndexLevel extends AbstractPollutionLevel
{
    protected array $levels = [
        0 => 2,
        1 => 5,
        2 => 8,
        3 => 11,
    ];
}
