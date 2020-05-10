<?php declare(strict_types=1);

namespace App\Provider\Luftdaten;

use App\Air\Measurement\PM10;
use App\Air\Measurement\PM25;
use App\Provider\AbstractProvider;
use App\Provider\Luftdaten\SourceFetcher\SourceFetcher;
use App\Provider\Luftdaten\StationLoader\LuftdatenStationLoader;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;

class LuftdatenProvider extends AbstractProvider
{
    const IDENTIFIER = 'ld';

    protected SourceFetcher $sourceFetcher;

    public function __construct(LuftdatenStationLoader $luftdatenStationLoader, SourceFetcher $sourceFetcher)
    {
        $this->sourceFetcher = $sourceFetcher;
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

    public function fetchMeasurements(FetchProcess $fetchProcess): FetchResult
    {
        return $this->sourceFetcher->fetch($fetchProcess);
    }
}
