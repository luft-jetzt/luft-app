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
}


