<?php declare(strict_types=1);

namespace App\Air\DataRetriever;

use Caldera\GeoBasic\Coord\CoordInterface;

class ChainedDataRetriever implements DataRetrieverInterface
{
    private array $chain = [];

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
    public function retrieveDataForCoord(CoordInterface $coord): array
    {
        $dataList = [];

        /** @var DataRetrieverInterface $dataRetriever */
        foreach ($this->chain as $dataRetriever) {
            $dataList = array_merge($dataList, $dataRetriever->retrieveDataForCoord($coord));
        }

        return $dataList;
    }
}
