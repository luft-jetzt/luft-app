<?php declare(strict_types=1);

namespace App\DataPurger;

use App\Entity\Data;
use App\Provider\ProviderInterface;
use Doctrine\Persistence\ManagerRegistry;

class DataPurger implements DataPurgerInterface
{
    protected ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function purgeData(\DateTimeInterface $untilDateTime, ProviderInterface $provider = null): int
    {
        $dataList = $this->managerRegistry->getRepository(Data::class)->findUntaggedInInterval(null, $untilDateTime, $provider);

        $counter = count($dataList);

        $em = $this->managerRegistry->getManager();

        foreach ($dataList as $data) {
            $em->remove($data);
        }

        $em->flush();

        return $counter;
    }
}
