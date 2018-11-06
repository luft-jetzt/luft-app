<?php declare(strict_types=1);

namespace App\AirQuality\PollutionLevel;

class COLevel extends AbstractPollutionLevel
{
    protected $levels = [1000, 2000, 4000, 10000, 30000];
}
