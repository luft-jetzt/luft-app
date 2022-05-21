<?php declare(strict_types=1);

namespace App\ImportCache;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class ImportCache implements ImportCacheInterface
{
    protected AdapterInterface $cacheAdapter;

    public function __construct(string $redisHost)
    {
        $this->cacheAdapter = new RedisAdapter(
            RedisAdapter::createConnection($redisHost),
            self::CACHE_NAMESPACE,
            self::TTL
        );
    }

    public function get(string $key): ?int
    {
        $cacheItem = $this->cacheAdapter->getItem($key);

        if (!$cacheItem->isHit()) {
            return null;
        }

        return (int) $cacheItem->get();
    }

    public function has(string $key): bool
    {
        return $this->cacheAdapter->hasItem($key);
    }

    public function set(string $key, int $timestamp): void
    {
        $cacheItem = $this->cacheAdapter->getItem($key);

        $cacheItem->set($timestamp);

        $this->cacheAdapter->save($cacheItem);
    }

    public function clear(): void
    {
        $this->cacheAdapter->clear();
    }
}
