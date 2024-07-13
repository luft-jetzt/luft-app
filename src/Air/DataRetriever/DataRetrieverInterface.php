<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

use Caldera\GeoBasic\Coord\CoordInterface;

interface DataRetrieverInterface
{
    public function retrieveDataForCoord(CoordInterface $coord): array;
}
