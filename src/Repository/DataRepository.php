<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data;
use App\Entity\Station;
use App\Util\DateTimeUtil;
use Doctrine\ORM\EntityRepository;

class DataRepository extends EntityRepository
{
    public function findLatestDataForStationAndPollutant(Station $station, int $pollutant, \DateTime $fromDateTime = null, \DateInterval $dateInterval, string $order = 'DESC'): ?Data
    {
        $qb = $this->createQueryBuilder('d');

        $qb
            ->where($qb->expr()->eq('d.station', ':station'))
            ->andWhere($qb->expr()->eq('d.pollutant', ':pollutant'))
            ->orderBy('d.dateTime', ':order')
            ->setMaxResults(1)
            ->setParameter('station', $station)
            ->setParameter('pollutant', $pollutant)
            ->setParameter('order', $order);

        if ($fromDateTime && $dateInterval) {
            $qb
                ->andWhere($qb->expr()->gt('d.dateTime', ':fromDateTime'))
                ->andWhere($qb->expr()->lte('d.dateTime', ':untilDateTime'))
                ->setParameter('fromDateTime', $fromDateTime)
                ->setParameter('untilDateTime', $fromDateTime->add($dateInterval));
        }

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findInInterval(\DateTime $fromDateTime, \DateTime $untilDateTime): array
    {
        $qb = $this->createQueryBuilder('d');

        $qb
            ->where($qb->expr()->gte('d.dateTime', ':fromDateTime'))
            ->andWhere($qb->expr()->lte('d.dateTime', ':untilDateTime'))
            ->setParameter('fromDateTime', $fromDateTime)
            ->setParameter('untilDateTime', $untilDateTime);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}

