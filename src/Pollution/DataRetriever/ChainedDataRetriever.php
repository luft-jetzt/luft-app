<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use Caldera\GeoBasic\Coord\CoordInterface;

class ChainedDataRetriever implements DataRetrieverInterface
{
    /** @var array $chain */
    protected $chain = [];

    public function __construct(CachedElasticDataRetriever $cachedElasticDataRetriever, Co2CachedDataRetriever $co2CachedDataRetriever)
    {
        $this->chain = [
            $cachedElasticDataRetriever,
            $co2CachedDataRetriever,
        ];
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array
    {
        $dataList = [];

        /** @var DataRetrieverInterface $dataRetriever */
        foreach ($this->chain as $dataRetriever) {
            $dataList = $dataList + $dataRetriever->retrieveDataForCoord($coord, $pollutantId, $fromDateTime, $dateInterval, $maxDistance, $maxResults);
        }

        return $dataList;
    }
}