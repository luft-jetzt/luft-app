<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class COLevel extends AbstractPollutionLevel
{
    protected array $levels = [1000, 2000, 4000, 10000, 30000];
}
