<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

use App\Entity\Data;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Persistence\ManagerRegistry;

class PostgisDataRetriever implements DataRetrieverInterface
{
    public function __construct(private readonly ManagerRegistry $managerRegistry)
    {

    }

    #[\Override]
    public function retrieveDataForCoord(CoordInterface $coord): array
    {
        $repository = $this->managerRegistry->getRepository(Data::class);

        return $repository->findCurrentDataForCoord($coord);
    }
}
