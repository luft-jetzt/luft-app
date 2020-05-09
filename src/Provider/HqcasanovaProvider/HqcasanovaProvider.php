<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider;

use App\Air\Measurement\CO2;
use App\Provider\AbstractProvider;
use App\Provider\HqcasanovaProvider\SourceFetcher\SourceFetcher;
use App\Provider\HqcasanovaProvider\StationLoader\HqcasanovaStationLoader;
use App\SourceFetcher\FetchProcess;

class HqcasanovaProvider extends AbstractProvider
{
    const IDENTIFIER = 'hqc';

    protected SourceFetcher $sourceFetcher;

    public function __construct(HqcasanovaStationLoader $stationLoader, SourceFetcher $sourceFetcher)
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

    public function fetchMeasurements(FetchProcess $fetchProcess): void
    {
        $this->sourceFetcher->fetch($fetchProcess);
    }
}
