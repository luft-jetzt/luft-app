<?php declare(strict_types=1);

namespace App\Pollution\StationCache;

use App\Entity\Station;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class StationCache implements StationCacheInterface
{
    const TTL = 3600;

    const CACHE_KEY = 'luft_stations';

    /** @var array $list */
    protected $list;

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var AbstractAdapter $cache */
    protected $cache;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->cache = $this->createConnection();

        $this->list = $this->loadFromCache() ?? $this->loadFromDatabase();

        $this->cacheStationList();
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function getStationByCode(string $stationCode): ?Station
    {
        if (!$this->stationExists($stationCode)) {
            return null;
        }

        return $this->list[$stationCode];
    }

    public function stationExists(string $stationCode): bool
    {
        return array_key_exists($stationCode, $this->list);
    }

    protected function createConnection(): AbstractAdapter
    {
        $client = RedisAdapter::createConnection('redis://localhost');

        $cache = new RedisAdapter($client);

        return $cache;
    }

    protected function loadFromDatabase(): array
    {
        return $this->registry->getRepository(Station::class)->findAllIndexed();
    }

    protected function loadFromCache(): ?array
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            return null;
        }

        return $cacheItem->get();
    }

    protected function cacheStationList(): void
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        $cacheItem
            ->set($this->list)
            ->expiresAfter(self::TTL);

        $this->cache->save($cacheItem);
    }
}
