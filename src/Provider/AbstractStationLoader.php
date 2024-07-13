<?php declare(strict_types=1);

namespace App\Provider;

use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractStationLoader implements StationLoaderInterface
{
    /** @var array $existingStationList */
    protected $existingStationList = [];

    /** @var array $newStationList */
    protected $newStationList = [];

    public function __construct(protected ManagerRegistry $registry)
    {
    }

    #[\Override]
    public function getNewStationList(): array
    {
        return $this->newStationList;
    }

    protected function stationExists(string $stationCode): bool
    {
        return array_key_exists($stationCode, $this->existingStationList) || array_key_exists($stationCode, $this->newStationList);
    }
}
