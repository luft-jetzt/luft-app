<?php declare(strict_types=1);

namespace App\Provider\Luftdaten;

use App\Air\Measurement\CO;
use App\Air\Measurement\NO2;
use App\Air\Measurement\O3;
use App\Air\Measurement\PM10;
use App\Air\Measurement\PM25;
use App\Air\Measurement\SO2;
use App\Provider\AbstractProvider;
use App\Provider\Luftdaten\StationLoader\LuftdatenStationLoader;

class LuftdatenProvider extends AbstractProvider
{
    const IDENTIFIER = 'ld';

    public function __construct(LuftdatenStationLoader $luftdatenStationLoader)
    {
        $this->stationLoader = $luftdatenStationLoader;
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function providedMeasurements(): array
    {
        return [
            PM10::class,
            PM25::class,
        ];
    }
}
