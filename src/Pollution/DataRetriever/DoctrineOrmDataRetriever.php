<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Data;
use App\Entity\Station;
use App\Repository\DataRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineOrmDataRetriever implements DataRetrieverInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function retrieveStationData(Station $station, int $pollutant, \DateTime $dateTime = null): ?Data
    {
        /** @var DataRepository $repository */
        $repository = $this->registry->getRepository(Data::class);

        return $repository->findLatestDataForStationAndPollutant($station, $pollutant, $dateTime);
    }
}
