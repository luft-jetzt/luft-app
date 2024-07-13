<?php declare(strict_types=1);

namespace App\Air\Provider;

use App\Air\Measurement\MeasurementInterface;

abstract class AbstractProvider implements ProviderInterface
{
    protected StationLoaderInterface $stationLoader;

    #[\Override]
    public function getStationLoader(): StationLoaderInterface
    {
        return $this->stationLoader;
    }

    #[\Override]
    public function providesMeasurement(MeasurementInterface $measurement): bool
    {
        return in_array($measurement::class, $this->providedMeasurements());
    }
}
