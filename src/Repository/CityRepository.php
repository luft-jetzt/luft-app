<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

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
