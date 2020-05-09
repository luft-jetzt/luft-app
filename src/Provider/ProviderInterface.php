<?php declare(strict_types=1);

namespace App\Provider;

use App\Air\Measurement\MeasurementInterface;

interface ProviderInterface
{
    public function getIdentifier(): string;

    public function getStationLoader(): StationLoaderInterface;

    public function providedMeasurements(): array;

    public function providesMeasurement(MeasurementInterface $measurement): bool;
}
