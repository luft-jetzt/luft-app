<?php declare(strict_types=1);

namespace App\Pollution\ValueCache;

use App\Provider\ProviderInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class ValueCache implements ValueCacheInterface
{
    public function addValuesToCache(ProviderInterface $provider, array $valueList): ValueCacheInterface
    {
        $client = RedisAdapter::createConnection('redis://localhost');

        $cache = new RedisAdapter($client);

        $key = sprintf('values-%s', $provider->getIdentifier());

        $item = $cache->getItem($key);

        $cacheList = $item->get() ?? [];

        $cacheList = array_merge($cacheList, $valueList);

        $item->set($cacheList);

        $cache->save($item);

        return $this;
    }
}
