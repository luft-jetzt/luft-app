<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Persister;

use App\Entity\Station;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractPersister implements PersisterInterface
{
    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var ObjectManager $entityManager */
    protected $entityManager;

    /** @var array $stationList */
    protected $stationList = [];

    /** @var array $newValueList */
    protected $newValueList = [];

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();

        $this->fetchStationList();
    }

    protected function fetchStationList(): PersisterInterface
    {
        $stations = $this->doctrine->getRepository(Station::class)->findAll();

        /** @var Station $station */
        foreach ($stations as $station) {
            $this->stationList[$station->getStationCode()] = $station;
        }

        return $this;
    }

    protected function stationExists(string $stationCode): bool
    {
        return array_key_exists($stationCode, $this->stationList);
    }

    protected function getStationByCode(string $stationCode): Station
    {
        return $this->stationList[$stationCode];
    }

    public function getNewValueList(): array
    {
        return $this->newValueList;
    }
}
