<?php declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\City;
use Doctrine\ORM\EntityRepository;

class TwitterScheduleRepository extends EntityRepository
{
    public function findByCity(City $city): array
    {
        $qb = $this->createQueryBuilder('ts');

        $qb
            ->where($qb->expr()->eq('ts.city', ':city'))
            ->setParameter('city', $city)
            ->orderBy('ts.station', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }
}

