<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use App\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }

    public function findAllIndexed(): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb->indexBy('s', 's.stationCode');

        return $qb->getQuery()->getResult();
    }

    public function findByProvider(string $providerIdentifier): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->eq('s.provider', ':provider'))
            ->setParameter('provider', $providerIdentifier);

        return $qb->getQuery()->getResult();
    }

    public function findWithoutCity(): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->isNull('s.city'))
            ->andWhere($qb->expr()->eq('s.provider', ':provider'))
            ->setParameter('provider', 'uba_de')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findActiveStations(): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->isNull('s.untilDate'))
            ->orderBy('s.stationCode');

        return $qb->getQuery()->getResult();
    }

    public function findActiveStationsByProvider(string $providerIdentifier): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->isNull('s.untilDate'))
            ->andWhere($qb->expr()->eq('s.provider', ':provider'))
            ->setParameter('provider', $providerIdentifier)
            ->orderBy('s.stationCode');

        return $qb->getQuery()->getResult();
    }

    public function findStationsByProvider(string $providerIdentifier): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->eq('s.provider', ':provider'))
            ->setParameter('provider', $providerIdentifier)
            ->orderBy('s.stationCode');

        return $qb->getQuery()->getResult();
    }

    public function findActiveStationsForCity(City $city): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->eq('s.city', ':city'))
            ->setParameter('city', $city)
            ->andWhere($qb->expr()->isNull('s.untilDate'))
            ->orderBy('s.stationCode');

        return $qb->getQuery()->getResult();
    }

    /**
     * Fallback für Städte ohne verknüpfte Stationen: die nächstgelegenen aktiven
     * Stationen im Umkreis um eine Koordinate (nach Entfernung sortiert).
     *
     * @return Station[]
     */
    public function findActiveStationsNearCoord(float $latitude, float $longitude, int $limit = 6, int $radiusMeters = 12000): array
    {
        $limit = max(1, min(50, $limit));
        $radiusMeters = max(1, min(100000, $radiusMeters));

        // Nur IDs per Scalar-Query (coord ist eine PostGIS-Geometry-Spalte – ORM-Hydration umgehen).
        $sql = sprintf(
            'WITH p AS (SELECT ST_SetSRID(ST_MakePoint(:lon, :lat), 4326) AS geom)
             SELECT s.id
             FROM station s, p
             WHERE s.until_date IS NULL
               AND ST_DWithin(s.coord::geography, p.geom::geography, %d)
             ORDER BY s.coord::geography <-> p.geom::geography ASC
             LIMIT %d',
            $radiusMeters,
            $limit,
        );

        $ids = $this->getEntityManager()->getConnection()
            ->executeQuery($sql, ['lon' => $longitude, 'lat' => $latitude])
            ->fetchFirstColumn();

        if (!$ids) {
            return [];
        }

        $stations = $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        // Reihenfolge nach Entfernung (aus dem Scalar-Query) wiederherstellen.
        $order = array_flip(array_map('intval', $ids));
        usort($stations, fn (Station $a, Station $b) => $order[$a->getId()] <=> $order[$b->getId()]);

        return $stations;
    }
}
