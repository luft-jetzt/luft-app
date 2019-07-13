<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use Caldera\GeoBasic\Coord\CoordInterface;

interface DataRetrieverInterface
{
    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array;
}
