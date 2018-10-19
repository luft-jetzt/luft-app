<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
    public function findCitiesWithActiveStations(): array
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->join('c.stations', 's')
            ->where($qb->expr()->isNull('s.untilDate'))
            ->orderBy('c.name', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }
}

