<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use Caldera\GeoBasic\Coord\CoordInterface;

interface TweakedElasticDataRetrieverInterface
{
    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, string $order = 'DESC', float $maxDistance = 20.0, int $maxResults): array;
}
