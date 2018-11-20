<?php declare(strict_types=1);

namespace App\Provider;

abstract class AbstractProvider implements ProviderInterface
{
    /** @var StationLoaderInterface $stationLoader */
    protected $stationLoader;

    protected $sourceFetcher;

    public function getStationLoader(): StationLoaderInterface
    {
        return $this->stationLoader;
    }

    public function getSourceFetcher()
    {
        return $this->sourceFetcher;
    }
}
