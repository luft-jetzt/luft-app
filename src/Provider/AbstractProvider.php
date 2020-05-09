<?php declare(strict_types=1);

namespace App\Provider;

use App\Air\Measurement\MeasurementInterface;

abstract class AbstractProvider implements ProviderInterface
{
    protected StationLoaderInterface $stationLoader;

    public function getStationLoader(): StationLoaderInterface
    {
        return $this->stationLoader;
    }

    public function providesMeasurement(MeasurementInterface $measurement): bool
    {
        return in_array(get_class($measurement), $this->providedMeasurements());
    }
}
