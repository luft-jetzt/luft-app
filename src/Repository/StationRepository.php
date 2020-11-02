<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class StationRepository extends EntityRepository
{
    public function findAllIndexed(): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb->indexBy('s', 's.stationCode');

        return $qb->getQuery()->getResult();
    }

    public function findByProvider(string $providerIdentifier): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->eq('s.provider', ':provider'))
            ->setParameter('provider', $providerIdentifier);

        return $qb->getQuery()->getResult();
    }

    public function findIndexedByProvider(string $providerIdentifier, string $indexedField = 'stationCode'): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->indexBy('s', sprintf('s.%s', $indexedField))
            ->where($qb->expr()->eq('s.provider', ':provider'))
            ->setParameter('provider', $providerIdentifier);

        return $qb->getQuery()->getResult();
    }

    public function findWithoutCity(): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb->where($qb->expr()->isNull('s.city'));

        return $qb->getQuery()->getResult();
    }

    public function findActiveStations(): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->isNull('s.untilDate'))
            ->orderBy('s.stationCode');

        return $qb->getQuery()->getResult();
    }

    public function findActiveStationsByProvider(string $providerIdentifier): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->isNull('s.untilDate'))
            ->andWhere($qb->expr()->eq('s.provider', ':provider'))
            ->setParameter('provider', $providerIdentifier)
            ->setMaxResults(10)
            ->orderBy('s.stationCode');

        return $qb->getQuery()->getResult();
    }

    public function findActiveStationsForCity(City $city): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where($qb->expr()->eq('s.city', ':city'))
            ->setParameter('city', $city)
            ->andWhere($qb->expr()->isNull('s.untilDate'))
            ->orderBy('s.stationCode');

        return $qb->getQuery()->getResult();
    }
}

