<?php declare(strict_types=1);

namespace App\Pollution\DataCache;

use App\Entity\Data;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class DataCache implements DataCacheInterface
{
    const TTL = 86400;

    /** @var AdapterInterface $cache */
    protected $cache;

    public function __construct()
    {
        $client = RedisAdapter::createConnection(
            'redis://localhost'
        );

        $this->cache = new RedisAdapter($client,'luft-data', self::TTL);
    }

    public function addData(Data $data): DataCacheInterface
    {
        $key = KeyGenerator::generateKey($data);

        $cacheItem = $this->cache->getItem($key);

        $cacheItem->set($data);

        $this->cache->save($cacheItem);

        return $this;
    }

    public function getData(string $key): ?Data
    {
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        return null;
    }
}
