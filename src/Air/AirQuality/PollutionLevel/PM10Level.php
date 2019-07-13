<?php declare(strict_types=1);

namespace App\Air\AirQuality\PollutionLevel;

class PM10Level extends AbstractPollutionLevel
{
    protected $levels = [10, 20, 35, 50, 100];
}
