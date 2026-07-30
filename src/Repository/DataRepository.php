<?php declare(strict_types=1);

namespace App\Repository;

use App\Air\Map\MapStationFactory;
use App\Air\Pollutant\PollutantInterface;
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

    /**
     * Liefert je Station und Schadstoff den jüngsten Messwert aus der Materialized View
     * current_data für die Übersichtskarte (siehe docs/sql/current_data-matview.sql).
     *
     * @param string|null $pollutantIdentifier Karten-Identifier aus MapStationFactory::POLLUTANT_IDS, null = alle
     * @param bool        $officialOnly        nur amtliche Stationen (UBA + BfS/DWD-Präfixe)
     * @param array|null  $bbox                [west, süd, ost, nord] in Grad
     */
    public function findCurrentDataForMap(?string $pollutantIdentifier = null, bool $officialOnly = true, ?array $bbox = null): array
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('station_code', 'stationCode')
            ->addScalarResult('title', 'title')
            ->addScalarResult('latitude', 'latitude', 'float')
            ->addScalarResult('longitude', 'longitude', 'float')
            ->addScalarResult('pollutant', 'pollutant', 'integer')
            ->addScalarResult('value', 'value', 'float')
            ->addScalarResult('date_time', 'dateTime', 'datetime')
            ->addScalarResult('provider', 'provider')
        ;

        $whereList = [];
        $parameterList = [];

        if ($pollutantIdentifier) {
            $pollutantIdList = MapStationFactory::POLLUTANT_IDS[$pollutantIdentifier] ?? null;

            if (!$pollutantIdList) {
                throw new \InvalidArgumentException(sprintf('Unknown map pollutant identifier "%s"', $pollutantIdentifier));
            }

            $placeholderList = [];

            foreach ($pollutantIdList as $pollutantId) {
                $parameterList[] = $pollutantId;
                $placeholderList[] = '?';
            }

            $whereList[] = sprintf('pollutant IN (%s)', implode(', ', $placeholderList));
        } else {
            // CO2 ist eine Langzeitmessung (in der MV bis zu 14 Tage alt) und gehört nicht auf die Karte
            $whereList[] = 'pollutant != ?';
            $parameterList[] = PollutantInterface::POLLUTANT_CO2;
        }

        if ($officialOnly) {
            $whereList[] = "(provider = 'uba_de' OR station_code ~ '^(BFS|UBA|GAAHI|DWD|TROPOS|IFA|BAUA)')";
        }

        if ($bbox) {
            $whereList[] = 'longitude BETWEEN ? AND ? AND latitude BETWEEN ? AND ?';
            $parameterList[] = $bbox[0];
            $parameterList[] = $bbox[2];
            $parameterList[] = $bbox[1];
            $parameterList[] = $bbox[3];
        }

        $sql = sprintf('SELECT DISTINCT ON (station_code, pollutant) station_code, title, latitude, longitude, pollutant, value, date_time, provider
FROM current_data
WHERE %s
ORDER BY station_code, pollutant, date_time DESC', implode(' AND ', $whereList));

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        foreach ($parameterList as $index => $parameter) {
            $query->setParameter($index + 1, $parameter);
        }

        return $query->getResult();
    }

    public function refreshMaterializedView(): void
    {
        $connection = $this->getEntityManager()->getConnection();

        $connection->executeStatement('REFRESH MATERIALIZED VIEW current_data');
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
