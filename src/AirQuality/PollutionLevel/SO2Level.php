<?php declare(strict_types=1);

namespace App\AirQuality\PollutionLevel;

class SO2Level extends AbstractPollutionLevel
{
    protected $levels = [25, 50, 120, 350, 1000];
}
