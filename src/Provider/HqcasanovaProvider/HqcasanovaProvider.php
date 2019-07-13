<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider;

use App\Provider\AbstractProvider;
use App\Provider\HqcasanovaProvider\StationLoader\HqcasanovaStationLoader;

class HqcasanovaProvider extends AbstractProvider
{
    const IDENTIFIER = 'hqc';

    public function __construct(HqcasanovaStationLoader $stationLoader)
    {
        $this->stationLoader = $stationLoader;
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
