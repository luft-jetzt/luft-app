<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data;
use App\Entity\Station;
use App\Geo\Coord\CoordInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class DataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Data::class);
    }

    public function findCurrentDataForCoord(CoordInterface $coord): array
    {
        $rsm = $this->createDataStationResultSetMapping('id');

        $sql = 'SELECT DISTINCT ON (pollutant, provider) id, value, pollutant, date_time,
                                             id AS station_id, title, latitude, longitude, station_code, station_type, provider,
                                             coord <-> ST_MakePoint(?, ?) AS dist
FROM current_data
ORDER BY pollutant ASC, provider ASC, dist ASC, date_time DESC
LIMIT 10';

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query
            ->setParameter(1, $coord->getLongitude())
            ->setParameter(2, $coord->getLatitude())
        ;

        return $query->getResult();
    }

    public function findCurrentDataForStation(Station $station): array
    {
        $rsm = $this->createDataStationResultSetMapping('id');

        $sql = 'SELECT DISTINCT ON (d.pollutant) d.id, d.value, d.pollutant, d.date_time,
s.id AS station_id, s.title, s.latitude, s.longitude, s.station_code, s.station_type, s.provider
FROM data AS d
INNER JOIN station AS s ON s.id = d.station_id
WHERE s.id = ?
ORDER BY d.pollutant ASC, d.date_time DESC
LIMIT 10';

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query
            ->setParameter(1, $station->getId())
        ;

        return $query->getResult();
    }

    public function refreshMaterializedView(): void
    {
        $connection = $this->getEntityManager()->getConnection();

        $connection->executeStatement('REFRESH MATERIALIZED VIEW current_data');
        $connection->executeStatement('REFRESH MATERIALIZED VIEW silvester_data');
    }

    private function createDataStationResultSetMapping(string $dataIdColumn): ResultSetMapping
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addEntityResult(Data::class, 'd')
            ->addFieldResult('d', $dataIdColumn, 'id')
            ->addFieldResult('d', 'value', 'value')
            ->addFieldResult('d', 'pollutant', 'pollutant')
            ->addFieldResult('d', 'date_time', 'dateTime')
            ->addJoinedEntityResult(Station::class, 's', 'd', 'station')
            ->addFieldResult('s', 'station_id', 'id')
            ->addFieldResult('s', 'title', 'title')
            ->addFieldResult('s', 'latitude', 'latitude')
            ->addFieldResult('s', 'longitude', 'longitude')
            ->addFieldResult('s', 'station_code', 'stationCode')
            ->addFieldResult('s', 'station_type', 'stationType')
            ->addFieldResult('s', 'provider', 'provider')
        ;

        return $rsm;
    }
}
