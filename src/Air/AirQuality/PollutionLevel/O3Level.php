<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class O3Level extends AbstractPollutionLevel
{
    protected $levels = [33, 65, 120, 180, 240];
}
