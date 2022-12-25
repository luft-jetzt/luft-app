<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data;
use App\Entity\Station;
use App\Provider\ProviderInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityRepository;

class DataRepository extends EntityRepository
{

    public function findCurrentDataForCoord(CoordInterface $coord): array
    {
        /** @var Connection $conn */
        $connection = $this->getEntityManager()->getConnection();
        $sql = '
SELECT DISTINCT ON (d.pollutant) d.id, d.value, d.pollutant, d.date_time, s.id, s.title, s.coord <-> ST_MakePoint(:longitude, :latitude) AS dist
FROM data AS d
JOIN station AS s ON d.station_id = s.id 
ORDER BY d.pollutant ASC, dist ASC, d.date_time DESC
LIMIT 10';

        /** @var Statement $stmt */
        $statement = $connection->prepare($sql);

        $params = [
            'latitude' => $coord->getLatitude(),
            'longitude' => $coord->getLongitude(),
        ];

        return $statement->executeQuery($params)->fetchAllAssociative();
    }
}
