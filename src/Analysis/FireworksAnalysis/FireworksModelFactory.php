<?php declare(strict_types=1);

namespace App\Analysis\FireworksAnalylsis;

use App\Entity\Data;
use Symfony\Bridge\Doctrine\RegistryInterface;

class FireworksModelFactory implements FireworksModelFactoryInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function convert(array $buckets): array
    {
        $resultList = [];

        foreach ($buckets as $bucket) {
            $slope = array_pop($bucket['derivative_agg']);

            foreach ($bucket['top_hits_agg']['hits']['hits'] as $hit) {
                /** @var Data $data */
                $data = $this->registry->getRepository(Data::class)->find(intval($hit['_id']));

                $resultList[] = new FireworksModel($data->getStation(), $data, $slope);
            }
        }

        return $this->sortResultList($resultList);
    }

    /**
     * @TODO this should be done in elasticsearch
     */
    protected function sortResultList(array $resultList = []): array
    {
        usort($resultList, function(FireworksModel $a, FireworksModel $b): int
        {
            if ($a->getSlope() === $b->getSlope()) {
                return 0;
            }
            return ($a->getSlope() > $b->getSlope()) ? -1 : 1;
        });

        return $resultList;
    }
}
