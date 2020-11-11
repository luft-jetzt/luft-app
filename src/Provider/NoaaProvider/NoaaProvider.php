<?php declare(strict_types=1);

namespace App\Provider\NoaaProvider;

use App\Air\Measurement\CO2;
use App\Provider\AbstractProvider;
use App\Provider\NoaaProvider\SourceFetcher\SourceFetcher;
use App\Provider\NoaaProvider\StationLoader\NoaaStationLoader;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;

class NoaaProvider extends AbstractProvider
{
    const IDENTIFIER = 'noaa';

    protected SourceFetcher $sourceFetcher;

    public function __construct(NoaaStationLoader $stationLoader, SourceFetcher $sourceFetcher)
    {
        $this->stationLoader = $stationLoader;
        $this->sourceFetcher = $sourceFetcher;
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

    public function fetchMeasurements(FetchProcess $fetchProcess): FetchResult
    {
        return $this->sourceFetcher->fetch($fetchProcess);
    }
}
