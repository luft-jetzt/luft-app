<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use Doctrine\ORM\EntityRepository;

class DataRepository extends EntityRepository
{
    public function findLatestDataForStationAndPollutant(Station $station, string $pollutant): ?Data
    {
        $qb = $this->createQueryBuilder('d');

        $qb
            ->where($qb->expr()->eq('d.station', $station->getId()))
            ->andWhere($qb->expr()->eq('d.pollutant', '\''.$pollutant.'\''))
            ->orderBy('d.dateTime', 'DESC')
            ->setMaxResults(1)
        ;

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}

