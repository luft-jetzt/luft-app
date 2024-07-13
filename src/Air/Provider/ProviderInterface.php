<?php declare(strict_types=1);

namespace App\Air\Provider;

use App\Air\Pollutant\PollutantInterface;
use App\Air\SourceFetcher\FetchProcess;
use App\Air\SourceFetcher\FetchResult;

interface ProviderInterface
{
    public function getIdentifier(): string;

    public function getStationLoader(): StationLoaderInterface;

    public function providedMeasurements(): array;

    public function providesMeasurement(PollutantInterface $measurement): bool;

    public function fetchMeasurements(FetchProcess $fetchProcess): FetchResult;
}
