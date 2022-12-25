<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use App\Entity\Data;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Persistence\ManagerRegistry;

class PostgisDataRetriever implements DataRetrieverInterface
{
    public function __construct(protected ManagerRegistry $managerRegistry)
    {

    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId = null, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array
    {
        $repository = $this->managerRegistry->getRepository(Data::class);

        $result = $repository->findCurrentDataForCoord($coord);

        dd($result);
    }
}