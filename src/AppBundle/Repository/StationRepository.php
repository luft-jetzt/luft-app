<?php declare(strict_types=1);

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class StationRepository extends EntityRepository
{
    public function findAllIndexed(): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->indexBy('s', 's.stationCode')
        ;

        return $qb->getQuery()->getResult();
    }
}

