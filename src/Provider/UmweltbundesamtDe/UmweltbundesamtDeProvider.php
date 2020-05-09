<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe;

use App\Air\Measurement\CO;
use App\Air\Measurement\NO2;
use App\Air\Measurement\O3;
use App\Air\Measurement\PM10;
use App\Air\Measurement\SO2;
use App\Provider\AbstractProvider;
use App\Provider\UmweltbundesamtDe\SourceFetcher\SourceFetcher;
use App\Provider\UmweltbundesamtDe\StationLoader\UmweltbundesamtStationLoader;
use App\SourceFetcher\FetchProcess;

class UmweltbundesamtDeProvider extends AbstractProvider
{
    const IDENTIFIER = 'uba_de';

    protected SourceFetcher $fetcher;

    public function __construct(UmweltbundesamtStationLoader $umweltbundesamtStationLoader, SourceFetcher $fetcher)
    {
        $this->stationLoader = $umweltbundesamtStationLoader;
        $this->fetcher = $fetcher;
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function providedMeasurements(): array
    {
        return [
            CO::class,
            NO2::class,
            O3::class,
            PM10::class,
            SO2::class,
        ];
    }

    public function fetchMeasurements(FetchProcess $fetchProcess): void
    {
        $this->fetcher->fetch($fetchProcess);
    }
}
