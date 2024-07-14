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
    public function providesPollutant(PollutantInterface $pollutant): bool
    {
        return in_array($pollutant::class, $this->providedPollutants());
    }
}
