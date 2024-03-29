<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class TemperatureLevel extends AbstractPollutionLevel
{
    protected array $levels = [
        0 => 23,
        1 => 28,
        2 => 33,
        3 => 38,
    ];
}
