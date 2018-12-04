<?php declare(strict_types=1);

namespace App\Analysis\KomfortofenAnalysis;

use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\StationCache\StationCacheInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class KomfortofenModelFactory implements KomfortofenModelFactoryInterface
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

                $resultList[] = new KomfortofenModel($data->getStation(), $data, $slope);
            }
        }

        return $resultList;
    }
}
