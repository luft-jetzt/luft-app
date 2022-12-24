<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class CoronaIncidenceLevel extends AbstractPollutionLevel
{
    protected array $levels = [
        0 => 10,
        2 => 35,
        4 => 50,
    ];
}
