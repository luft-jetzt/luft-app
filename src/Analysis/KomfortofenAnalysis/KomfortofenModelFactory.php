<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Entity\Data;
use App\Pollution\DataFinder\DataConverterInterface;

class KomfortofenModelFactory implements KomfortofenModelFactoryInterface
{
    public function __construct(protected DataConverterInterface $dataConverter)
    {
    }

    #[\Override]
    public function convert(array $buckets): array
    {
        $resultList = [];

        foreach ($buckets as $bucket) {
            $slope = array_pop($bucket['derivative_agg']);

            foreach ($bucket['top_hits_agg']['hits']['hits'] as $hit) {
                /** @var Data $data */
                $data = $this->dataConverter->convertArray($hit['_source']);

                $resultList[] = new KomfortofenModel($data->getStation(), $data, $slope);
            }
        }

        return $this->sortResultList($resultList);
    }

    /**
     * @TODO this should be done in elasticsearch
     */
    protected function sortResultList(array $resultList = []): array
    {
        usort($resultList, fn(KomfortofenModel $a, KomfortofenModel $b): int => $b->getSlope() <=> $a->getSlope());

        return $resultList;
    }
}
