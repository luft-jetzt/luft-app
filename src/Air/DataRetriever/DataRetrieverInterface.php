<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

use App\Geo\Coord\CoordInterface;

interface DataRetrieverInterface
{
    public function retrieveDataForCoord(CoordInterface $coord): array;
}
