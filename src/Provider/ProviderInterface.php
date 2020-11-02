<?php declare(strict_types=1);

namespace App\Provider;

use App\Air\Measurement\MeasurementInterface;
use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;

interface ProviderInterface
{
    public function getIdentifier(): string;

    public function getStationLoader(): StationLoaderInterface;

    public function providedMeasurements(): array;

    public function providesMeasurement(MeasurementInterface $measurement): bool;

    public function fetchMeasurements(FetchProcess $fetchProcess): FetchResult;
}
