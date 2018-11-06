<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data;
use App\Entity\Station;
use App\Util\DateTimeUtil;
use Doctrine\ORM\EntityRepository;

class DataRepository extends EntityRepository
{
    public function findLatestDataForStationAndPollutant(Station $station, int $pollutant, \DateTime $dateTime = null): ?Data
    {
        $qb = $this->createQueryBuilder('d');

        $qb
            ->where($qb->expr()->eq('d.station', ':station'))
            ->andWhere($qb->expr()->eq('d.pollutant', ':pollutant'))
            ->orderBy('d.dateTime', 'DESC')
            ->setMaxResults(1)
            ->setParameter('station', $station)
            ->setParameter('pollutant', $pollutant);

        if ($dateTime) {
            $qb
                ->andWhere($qb->expr()->gte('d.dateTime', ':fromDateTime'))
                ->andWhere($qb->expr()->lte('d.dateTime', ':untilDateTime'))
                ->setParameter('fromDateTime', DateTimeUtil::getHourStartDateTime($dateTime))
                ->setParameter('untilDateTime', DateTimeUtil::getHourEndDateTime($dateTime));
        }

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}

