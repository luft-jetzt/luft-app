<?php declare(strict_types=1);

namespace App\Pollution\StationCache;

use App\Entity\Station;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class StationCache implements StationCacheInterface
{
    final public const int TTL = 3;
    final public const string CACHE_KEY = 'luft_stations';
    protected array $list = [];
    protected AbstractAdapter $cache;

    public function __construct(protected ManagerRegistry $registry, string $redisHost)
    {
        $this->cache = $this->createConnection($redisHost);

        if (!$this->list = $this->loadFromCache()) {
            $this->list = $this->loadFromDatabase();

            $this->cacheStationList();
        }
    }

    #[\Override]
    public function getList(): array
    {
        return $this->list;
    }

    #[\Override]
    public function getStationReferenceByCode(string $stationCode): ?Station
    {
        if (!$this->stationExists($stationCode)) {
            return null;
        }

        $reference = $this->registry->getManager()->getReference(Station::class, $this->getStationByCode($stationCode)->getId());

        return $reference;
    }

    #[\Override]
    public function getStationByCode(string $stationCode): ?Station
    {
        if (!$this->stationExists($stationCode)) {
            return null;
        }

        return $this->list[$stationCode];
    }

    #[\Override]
    public function stationExists(string $stationCode): bool
    {
        return array_key_exists($stationCode, $this->list);
    }

    protected function createConnection(string $redisHost): AbstractAdapter
    {
        $client = RedisAdapter::createConnection($redisHost);

        $cache = new RedisAdapter($client);

        return $cache;
    }

    protected function loadFromDatabase(): array
    {
        return $this->registry->getRepository(Station::class)->findAllIndexed();
    }

    protected function loadFromCache(): array
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            return [];
        }

        return $cacheItem->get();
    }

    protected function cacheStationList(): void
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        foreach ($this->list as $station) {
            $this->registry->getManager()->detach($station);
        }

        $cacheItem
            ->set($this->list)
            ->expiresAfter(self::TTL);

        $this->cache->save($cacheItem);
    }
}
