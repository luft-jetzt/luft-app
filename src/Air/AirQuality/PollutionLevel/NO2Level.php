<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class NO2Level extends AbstractPollutionLevel
{
    protected array $levels = [25, 50, 100, 200, 500];
}
