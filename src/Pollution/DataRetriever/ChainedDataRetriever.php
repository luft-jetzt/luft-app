<?php declare(strict_types=1);

namespace App\Pollution\DataRetriever;

use Caldera\GeoBasic\Coord\CoordInterface;

class ChainedDataRetriever implements DataRetrieverInterface
{
    protected array $chain = [];

    public function __construct(ElasticDataRetriever $elasticDataRetriever, Co2CachedDataRetriever $co2CachedDataRetriever, AdhocDataRetriever $adhocDataRetriever)
    {
        $this->chain = [
            $elasticDataRetriever,
            $co2CachedDataRetriever,
            $adhocDataRetriever,
        ];
    }

    public function retrieveDataForCoord(CoordInterface $coord, int $pollutantId = null, \DateTime $fromDateTime = null, \DateInterval $dateInterval = null, float $maxDistance = 20.0, int $maxResults = 250): array
    {
        $dataList = [];

        /** @var DataRetrieverInterface $dataRetriever */
        foreach ($this->chain as $dataRetriever) {
            $dataList = array_merge($dataList, $dataRetriever->retrieveDataForCoord($coord, $pollutantId, $fromDateTime, $dateInterval, $maxDistance, $maxResults));
        }

        return $dataList;
    }
}
