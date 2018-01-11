<?php declare(strict_types=1);

namespace AppBundle\Pollution\DataRetriever;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use AppBundle\Repository\DataRepository;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class DoctrineOrmDataRetriever implements DataRetrieverInterface
{
    protected $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function retrieveStationData(Station $station, string $pollutant): ?Data
    {
        /** @var DataRepository $repository */
        $repository = $this->doctrine->getRepository(Data::class);

        return $repository->findLatestDataForStationAndPollutant($station, $pollutant);
    }
}
