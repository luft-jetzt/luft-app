<?php declare(strict_types=1);

namespace App\Provider;

use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractStationLoader implements StationLoaderInterface
{
    protected array $existingStationList = [];
    protected array $newStationList = [];
    protected RegistryInterface $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function getNewStationList(): array
    {
        return $this->newStationList;
    }

    protected function stationExists(string $stationCode): bool
    {
        return array_key_exists($stationCode, $this->existingStationList) || array_key_exists($stationCode, $this->newStationList);
    }
}
