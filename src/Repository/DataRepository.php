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
        ;

        $sql = 'SELECT DISTINCT ON (d.pollutant) d.id, d.value, d.pollutant, d.date_time, s.id AS station_id, s.title, s.coord <-> ST_MakePoint(?, ?) AS dist
FROM data AS d
INNER JOIN station AS s ON s.id = d.station_id 
ORDER BY d.pollutant ASC, dist ASC, d.date_time DESC
LIMIT 10';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query
            ->setParameter(1, $coord->getLongitude())
            ->setParameter(2, $coord->getLatitude())
        ;

        return $query->getResult();
    }
}
