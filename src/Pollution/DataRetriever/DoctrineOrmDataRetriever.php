<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Data;
use App\Entity\Station;
use App\Repository\DataRepository;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class DoctrineOrmDataRetriever implements DataRetrieverInterface
{
    protected $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function retrieveStationData(Station $station, int $pollutant): ?Data
    {
        /** @var DataRepository $repository */
        $repository = $this->doctrine->getRepository(Data::class);

        return $repository->findLatestDataForStationAndPollutant($station, $pollutant);
    }
}
