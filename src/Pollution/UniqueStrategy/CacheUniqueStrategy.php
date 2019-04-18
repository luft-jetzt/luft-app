<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;
use App\Pollution\Value\Value;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheUniqueStrategy implements UniqueStrategyInterface
{
    const CACHE_KEY = 'luft-data';
    const TTL = 172800;

    /** @var array $existentDataList */
    protected $existentDataList = [];

    /** @var AdapterInterface $cacheAdapter */
    protected $cacheAdapter;

    public function __construct(AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    public function __destruct()
    {
        $this->save();
    }

    public function init(array $values): UniqueStrategyInterface
    {
        $cacheItem = $this->cacheAdapter->getItem(self::CACHE_KEY);

        if ($cacheItem->isHit()) {
            $this->existentDataList = $cacheItem->get();
        } else {
            $this->existentDataList = [];
        }

        return $this;
    }

    public function isDataDuplicate(Data $data): bool
    {
        $hash = $this->hashData($data);

        return array_key_exists($hash, $this->existentDataList);
    }


    public function addData(Data $data): UniqueStrategyInterface
    {
        $hash = $this->hashData($data);

        $this->existentDataList[$hash] = $data->getDateTime()->format('U');

        return $this;
    }

    public function addDataList(array $dataList): UniqueStrategyInterface
    {
        /** @var Data $data */
        foreach ($dataList as $key => $data) {
            $this->addData($data);
        }

        return $this;
    }

    public function save(): CacheUniqueStrategy
    {
        $cacheItem = $this->cacheAdapter->getItem(self::CACHE_KEY);

        if ($cacheItem->isHit()) {
            $existentDataList = $cacheItem->get();
        } else {
            $existentDataList = [];
        }

        $existentDataList = $this->existentDataList + $existentDataList;

        $limitTimestamp = (new \DateTime())->sub(new \DateInterval(sprintf('PT%dS', self::TTL)))->format('U');

        /** @var Data $data */
        foreach ($existentDataList as $key => $timestamp) {
            if ($timestamp < $limitTimestamp) {
                unset($existentDataList[$key]);
            }
        }

        $cacheItem->set($existentDataList);

        $this->cacheAdapter->save($cacheItem);

        return $this;
    }

    public function clear(): CacheUniqueStrategy
    {
        $cacheItem = $this->cacheAdapter->getItem(self::CACHE_KEY);

        $cacheItem->set([]);

        $this->cacheAdapter->save($cacheItem);

        return $this;
    }

    protected function hashData(Data $data): string
    {
        return $data->getStationId() . $data->getDateTime()->format('U') . $data->getPollutant() . $data->getValue();
    }

    protected function hashValue(Value $value): string
    {
        return $value->getStation() . $value->getDateTime()->format('U') . $value->getPollutant() . $value->getValue();
    }
}