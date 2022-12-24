<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class SO2Level extends AbstractPollutionLevel
{
    protected array $levels = [
        25,
        50,
        120,
        350,
        1000,
        ];
}
