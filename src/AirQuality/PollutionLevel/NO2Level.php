<?php declare(strict_types=1);

namespace App\AirQuality\PollutionLevel;

class NO2Level extends AbstractPollutionLevel
{
    protected $levels = [25, 50, 100, 200, 500];
}
