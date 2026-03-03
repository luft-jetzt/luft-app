<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

use App\Entity\Station;
use App\Geo\Coord\CoordInterface;
use App\Repository\DataRepository;

class PostgisDataRetriever implements DataRetrieverInterface
{
    public function __construct(private readonly DataRepository $dataRepository)
    {

    }

    #[\Override]
    public function retrieveDataForCoord(CoordInterface $coord): array
    {
        if ($coord instanceof Station) {
            return $this->dataRepository->findCurrentDataForStation($coord);
        }

        return $this->dataRepository->findCurrentDataForCoord($coord);
    }
}
