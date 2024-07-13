<?php declare(strict_types=1);

namespace App\Air\Provider;

use App\Air\Pollutant\PollutantInterface;

abstract class AbstractProvider implements ProviderInterface
{
    protected StationLoaderInterface $stationLoader;

    #[\Override]
    public function getStationLoader(): StationLoaderInterface
    {
        return $this->stationLoader;
    }

    #[\Override]
    public function providesMeasurement(PollutantInterface $measurement): bool
    {
        return in_array($measurement::class, $this->providedMeasurements());
    }
}
