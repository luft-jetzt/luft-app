<?php declare(strict_types=1);

namespace App\Analysis\FireworksAnalysis;

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

    public function convert(array $dataResult): array
    {
        $resultList = [];

        foreach ($dataResult as $data) {
           $resultList[] = new FireworksModel($data->getStation(), $data, 0);
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
            if ($a->getData()->getDateTime() === $b->getData()->getDateTime()) {
                return 0;
            }
            return ($a->getData()->getDateTime() > $b->getData()->getDateTime()) ? -1 : 1;
        });

        return $resultList;
    }
}
