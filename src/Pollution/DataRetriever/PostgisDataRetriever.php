<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\Persistence\ManagerRegistry;

class PostgisDataRetriever implements DataRetrieverInterface
{
    public function __construct(protected ManagerRegistry $managerRegistry)
    {

    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId = null, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection();

        $sql = 'SELECT id, title, coord <-> ST_MakePoint(53.234211, 10.4104193) AS dist
FROM station
ORDER BY dist LIMIT 10';

        /** @var Statement $statement */
        $statement = $connection->prepare($sql);
        $result = $statement->executeQuery();

        dd($result->fetchAllAssociative());
    }
}