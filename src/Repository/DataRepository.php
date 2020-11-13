<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data;
use App\Entity\Station;
use App\Provider\ProviderInterface;
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

    public function findInInterval(\DateTimeInterface $fromDateTime = null, \DateTimeInterface $untilDateTime = null, ProviderInterface $provider = null): array
    {
        $qb = $this->createQueryBuilder('d');

        if ($fromDateTime) {
            $qb
                ->andWhere($qb->expr()->gte('d.dateTime', ':fromDateTime'))
                ->setParameter('fromDateTime', $fromDateTime);
        }

        if ($untilDateTime) {
            $qb
                ->andWhere($qb->expr()->lte('d.dateTime', ':untilDateTime'))
                ->setParameter('untilDateTime', $untilDateTime);
        }

        if ($provider) {
            $qb
                ->join('d.station', 's')
                ->andWhere($qb->expr()->eq('s.provider', ':providerIdentifier'))
                ->setParameter('providerIdentifier', $provider->getIdentifier());
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findHashsInterval(\DateTimeInterface $fromDateTime, \DateTimeInterface $untilDateTime, array $stationList = []): array
    {
        $sql = 'SELECT CONCAT(d.station_id, UNIX_TIMESTAMP(d.date_time), d.pollutant, d.value) AS hash FROM data AS d';

        if (0 !== count($stationList)) {
            $sql.= ' JOIN station AS s ON d.station_id = s.id';
        }

        $sql.= ' WHERE d.date_time >= \''.$fromDateTime->format('Y-m-d H:i:s').'\' AND d.date_time <= \''.$untilDateTime->format('Y-m-d H:i:s').'\'';

        if (0 !== count($stationList)) {
            $sql.= ' AND s.station_code IN (\''.implode('\', \'', $stationList).'\')';
        }

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findForAnalysis(Station $station, int $pollutant, \DateTimeInterface $fromDateTime = null, \DateTimeInterface $untilDateTime = null): array
    {
        $qb = $this->createQueryBuilder('d');

        $qb
            ->where($qb->expr()->eq('d.station', ':station'))
            ->setParameter('station', $station)
            ->andWhere($qb->expr()->eq('d.pollutant', ':pollutant'))
            ->setParameter('pollutant', $pollutant)
            ->leftJoin(
                'App\Entity\Data',
                'd2',
                'WITH',
                'd2.pollutant = d.pollutant AND d2.station = d.station AND d2.value > d.value'
            )
            ->andWhere($qb->expr()->isNull('d2.value'))
            ->orderBy('d.dateTime','ASC');


        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function deleteData(\DateTimeInterface $untilDateTime, ProviderInterface $provider = null): int
    {
        $qb = $this->createQueryBuilder('data')->select('data.id');

        if ($provider) {
            $qb
                ->join('data.station', 's')
                ->where('s.provider = :provider')
                ->setParameter('provider', $provider->getIdentifier())
            ;
        }

        $ids = $qb
            ->getQuery()
            ->getResult();

        $this->createQueryBuilder('data')
            ->where('data.id in (:ids)')
            ->setParameter('ids', $ids)
            ->delete()
            ->getQuery()
            ->execute();

        return count($ids);
    }
}

