<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

use Caldera\GeoBasic\Coord\CoordInterface;

class ChainedDataRetriever implements DataRetrieverInterface
{
    protected array $chain = [];

    public function __construct(
        PostgisDataRetriever $postgisDataRetriever,
        AdhocDataRetriever $adhocDataRetriever
    )
    {
        $this->chain = [
            $postgisDataRetriever,
            $adhocDataRetriever,
        ];
    }

    #[\Override]
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
