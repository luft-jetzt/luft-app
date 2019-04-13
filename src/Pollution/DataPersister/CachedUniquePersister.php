<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Data;
use App\Pollution\StationCache\StationCacheInterface;
use App\Pollution\Value\Value;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CachedUniquePersister extends Persister implements UniquePersisterInterface
{
    const CACHE_KEY = 'luft-data';
    const TTL = 172800;

    /** @var array $existentDataList */
    protected $existentDataList = [];

    /** @var array $duplicateDataList */
    protected $duplicateDataList = [];

    protected $cacheAdapter;

    public function __construct(RegistryInterface $doctrine, StationCacheInterface $stationCache, AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;

        parent::__construct($doctrine, $stationCache);
    }

    protected function fetchExistentData(array $values): UniquePersister
    {
        $cacheItem = $this->cacheAdapter->getItem(self::CACHE_KEY);

        if ($cacheItem->isHit()) {
            $existentDataList = $cacheItem->get();
        } else {
            $existentDataList = [];
        }

        /** @var Data $data */
        foreach ($existentDataList as $key => $value) {
            $this->existentDataList[$value['hash']] = true;

            unset($existentDataList[$key]);
        }

        return $this;
    }

    protected function hashData(Data $data): string
    {
        return $data->getStationId() . $data->getDateTime()->format('U') . $data->getPollutant() . $data->getValue();
    }

    protected function dataExists(Data $data): bool
    {
        $hash = $this->hashData($data);

        return array_key_exists($hash, $this->existentDataList);
    }

    public function reset(): PersisterInterface
    {
        $this->existentDataList = [];
        $this->duplicateDataList = [];

        return parent::reset();
    }

    public function persistValues(array $values): PersisterInterface
    {
        if (0 === count($values)) {
            return $this;
        }

        $this->existentDataList = [];
        $this->fetchExistentData($values);

        /** @var Value $value */
        foreach ($values as $value) {
            $data = new Data();

            $data
                ->setDateTime($value->getDateTime())
                ->setValue($value->getValue())
                ->setPollutant($value->getPollutant());

            if ($this->stationExists($value->getStation())) {
                $data->setStation($this->getStationByCode($value->getStation()));
            } else {
                continue;
            }

            if ($this->dataExists($data)) {
                $this->duplicateDataList[] = $data;

                continue;
            }

            $this->existentDataList[$this->hashData($data)] = true;

            $this->entityManager->persist($data);

            $this->newValueList[] = $data;
        }

        $this->entityManager->flush();

        $this->syncCache($this->newValueList);

        return $this;
    }

    public function getDuplicateDataList(): array
    {
        return $this->duplicateDataList;
    }

    protected function syncCache(array $valueList): CachedUniquePersister
    {
        $cacheItem = $this->cacheAdapter->getItem(self::CACHE_KEY);

        if ($cacheItem->isHit()) {
            $existentDataList = $cacheItem->get();
        } else {
            $existentDataList = [];
        }

        $existentDataList = array_merge($existentDataList, $valueList);

        $limitDateTime = (new \DateTime())->sub(new \DateInterval(sprintf('PT%dS', self::TTL)));

        /** @var Data $data */
        foreach ($existentDataList as $key => $data) {
            if ($data->getDateTime() < $limitDateTime) {
                unset($existentDataList[$key]);
            }
        }

        $cacheItem->set($existentDataList);

        $this->cacheAdapter->save($cacheItem);

        return $this;
    }
}
