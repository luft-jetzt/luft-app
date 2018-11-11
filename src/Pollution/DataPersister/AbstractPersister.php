<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Station;
use App\Provider\ProviderInterface;
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

    /** @var ProviderInterface $provider */
    protected $provider;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();
    }

    public function setProvider(ProviderInterface $provider): PersisterInterface
    {
        $this->provider = $provider;
        
        return $this;
    }

    protected function fetchStationList(): PersisterInterface
    {
        $this->stationList = $this->doctrine->getRepository(Station::class)->findIndexedByProvider($this->provider->getIdentifier());

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
