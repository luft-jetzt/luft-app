<?php declare(strict_types=1);

namespace App\Pollution\DataCache;

use App\Entity\Data;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class DataCache implements DataCacheInterface
{
    const TTL = 60 * 60 * 8 * 5;
    const NAMESPACE = 'luft-data';

    protected AdapterInterface $cache;

    protected SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer, string $redisHost)
    {
        $this->serializer = $serializer;

        $client = RedisAdapter::createConnection($redisHost);

        $this->cache = new RedisAdapter($client,self::NAMESPACE, self::TTL);
    }

    public function addData(Data $data): DataCacheInterface
    {
        $key = KeyGenerator::generateKeyForData($data);

        $cacheItem = $this->cache->getItem($key);

        $cacheItem->set($this->serializer->serialize($data, 'json'));

        $this->cache->save($cacheItem);

        return $this;
    }

    public function getData(string $key): ?Data
    {
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit()) {
            return $this->serializer->deserialize($cacheItem->get(), Data::class, 'json');
        }

        return null;
    }
}
