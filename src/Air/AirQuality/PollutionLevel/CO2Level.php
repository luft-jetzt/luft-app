<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class CO2Level extends AbstractPollutionLevel
{
    protected array $levels = [
        3 => 300,
        4 => 350,
    ];
}
