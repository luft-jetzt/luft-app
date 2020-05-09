<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider;

use App\Air\Measurement\CO;
use App\Air\Measurement\CO2;
use App\Air\Measurement\NO2;
use App\Air\Measurement\O3;
use App\Air\Measurement\PM10;
use App\Air\Measurement\SO2;
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

    public function providedMeasurements(): array
    {
        return [
            CO2::class,
        ];
    }
}
