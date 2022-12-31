<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data;
use App\Entity\Station;
use App\Provider\ProviderInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class DataRepository extends EntityRepository
{

    public function findCurrentDataForCoord(CoordInterface $coord): array
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addEntityResult(Data::class, 'd')
            ->addFieldResult('d', 'id', 'id')
            ->addFieldResult('d', 'value', 'value')
            ->addFieldResult('d', 'pollutant', 'pollutant')
            ->addFieldResult('d', 'date_time', 'dateTime')
            ->addJoinedEntityResult(Station::class, 's', 'd', 'station')
            ->addFieldResult('s', 'station_id', 'id')
            ->addFieldResult('s', 'title', 'title')
            ->addFieldResult('s', 'latitude', 'latitude')
            ->addFieldResult('s', 'longitude', 'longitude')
            ->addFieldResult('s', 'station_code', 'stationCode')
            ->addFieldResult('s', 'title', 'title')
            ->addFieldResult('s', 'station_type', 'stationType')
            ->addFieldResult('s', 'provider', 'provider')
        ;

        $sql = 'SELECT DISTINCT ON (d.pollutant, s.provider) d.id, d.value, d.pollutant, d.date_time, 
s.id AS station_id, s.title, s.latitude, s.longitude, s.station_code, s.title, s.station_type, s.provider,
s.coord <-> ST_MakePoint(?, ?) AS dist
FROM data AS d
INNER JOIN station AS s ON s.id = d.station_id 
ORDER BY d.pollutant ASC, s.provider ASC, dist ASC, d.date_time DESC
LIMIT 10';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query
            ->setParameter(1, $coord->getLongitude())
            ->setParameter(2, $coord->getLatitude())
        ;

        //dd($query->getResult());
        return $query->getResult();
    }

    public function findCurrentDataForStation(Station $station): array
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addEntityResult(Data::class, 'd')
            ->addFieldResult('d', 'id', 'id')
            ->addFieldResult('d', 'value', 'value')
            ->addFieldResult('d', 'pollutant', 'pollutant')
            ->addFieldResult('d', 'date_time', 'dateTime')
            ->addJoinedEntityResult(Station::class, 's', 'd', 'station')
            ->addFieldResult('s', 'station_id', 'id')
            ->addFieldResult('s', 'title', 'title')
            ->addFieldResult('s', 'latitude', 'latitude')
            ->addFieldResult('s', 'longitude', 'longitude')
            ->addFieldResult('s', 'station_code', 'stationCode')
            ->addFieldResult('s', 'title', 'title')
            ->addFieldResult('s', 'station_type', 'stationType')
            ->addFieldResult('s', 'provider', 'provider')
        ;

        $sql = 'SELECT DISTINCT ON (d.pollutant) d.id, d.value, d.pollutant, d.date_time,
s.id AS station_id, s.title, s.latitude, s.longitude, s.station_code, s.title, s.station_type, s.provider
FROM data AS d
INNER JOIN station AS s ON s.id = d.station_id 
WHERE s.id = ?
ORDER BY d.pollutant ASC, d.date_time DESC
LIMIT 10';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query
            ->setParameter(1, $station->getId())
        ;

        //dd($query->getResult());
        return $query->getResult();
    }

    public function findDataForCoronaFireworksAnalysis(CoordInterface $coord): array
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addEntityResult(Data::class, 'd')
            ->addFieldResult('d', 'data_id', 'id')
            ->addFieldResult('d', 'value', 'value')
            ->addFieldResult('d', 'pollutant', 'pollutant')
            ->addFieldResult('d', 'date_time', 'dateTime')
            ->addJoinedEntityResult(Station::class, 's', 'd', 'station')
            ->addFieldResult('s', 'station_id', 'id')
            ->addFieldResult('s', 'title', 'title')
            ->addFieldResult('s', 'latitude', 'latitude')
            ->addFieldResult('s', 'longitude', 'longitude')
            ->addFieldResult('s', 'station_code', 'stationCode')
            ->addFieldResult('s', 'title', 'title')
            ->addFieldResult('s', 'station_type', 'stationType')
            ->addFieldResult('s', 'provider', 'provider')
        ;

        $sql = 'SELECT DISTINCT ON (date_trunc(\'hour\', date_time)) data_id, value, pollutant, date_time, station_id, title, latitude, longitude, station_code, station_type, provider
        FROM data_view
        WHERE station_id IN (SELECT id FROM station WHERE coord <-> ST_MakePoint(?, ?) < 2 ORDER BY coord <-> ST_MakePoint(?, ?) ASC)
        AND pollutant = 1
        AND ((DATE_PART(\'day\', date_time) = 31 AND DATE_PART(\'hour\', date_time) > 17) OR (DATE_PART(\'day\', date_time) = 1 AND DATE_PART(\'hour\', date_time) < 7))
        ORDER BY date_trunc(\'hour\', date_time), coord <-> ST_MakePoint(?, ?) ASC, value DESC';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query
            ->setParameter(1, $coord->getLongitude())
            ->setParameter(2, $coord->getLatitude())
            ->setParameter(3, $coord->getLongitude())
            ->setParameter(4, $coord->getLatitude())
            ->setParameter(5, $coord->getLongitude())
            ->setParameter(6, $coord->getLatitude())
        ;

        return $query->getResult();
    }

    public function refreshMaterializedView(): void
    {
        $sql = 'REFRESH MATERIALIZED VIEW data_view;';

        $query = $this->_em->createNativeQuery($sql, new ResultSetMapping());
        $query->getResult();
    }
}
