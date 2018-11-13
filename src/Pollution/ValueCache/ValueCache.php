<?php declare(strict_types=1);

namespace App\Pollution\ValueCache;

use App\Provider\ProviderInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;

class ValueCache implements ValueCacheInterface
{
    /** @var AbstractAdapter $cache */
    protected $cache;

    /** @var ProviderInterface $provider */
    protected $provider;

    public function __construct()
    {
        $this->cache = $this->createConnection();
    }

    public function setProvider(ProviderInterface $provider): ValueCacheInterface
    {
        $this->provider = $provider;

        return $this;
    }

    protected function createConnection(): AbstractAdapter
    {
        $client = RedisAdapter::createConnection('redis://localhost');

        $cache = new RedisAdapter($client);

        return $cache;
    }

    protected function getPagination(ProviderInterface $provider): array
    {
        $key = sprintf('values-%s-list', $provider->getIdentifier());

        $listItem = $this->cache->getItem($key);

        return $listItem->get() ?? [];
    }

    protected function savePagination(ProviderInterface $provider, array $pagination): ValueCache
    {
        $key = sprintf('values-%s-list', $provider->getIdentifier());

        $listItem = $this->cache->getItem($key);

        $listItem->set($pagination);

        $this->cache->save($listItem);

        return $this;
    }

    protected function addPage(ProviderInterface $provider, array $valueList): int
    {
        $pagination = $this->getPagination($provider);

        $nextPage = 0 === count($pagination) ? 1 : max(array_keys($pagination)) + 1;

        $pagination[$nextPage] = count($valueList);

        $this->savePagination($provider, $pagination);

        $key = sprintf('values-%s-%d', $provider->getIdentifier(), $nextPage);

        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($valueList);
        $this->cache->save($cacheItem);

        return $nextPage;
    }

    protected function getPage(ProviderInterface $provider, int $pageNumber): array
    {
        $key = sprintf('values-%s-%d', $provider->getIdentifier(), $pageNumber);

        $pageItem = $this->cache->getItem($key);

        return $pageItem->get() ?? [];
    }

    public function getNewestPage(ProviderInterface $provider): array
    {
        $pagination = $this->getPagination($provider);

        if (!$pagination) {
            return [];
        }

        $pageNumber = max(array_keys($pagination));

        unset($pagination[$pageNumber]);

        $this->savePagination($provider, $pagination);

        $page = $this->getPage($provider, $pageNumber);

        return $page;
    }

    public function addValuesToCache(ProviderInterface $provider, array $valueList): ValueCacheInterface
    {
        if (0 !== count($valueList)) {
            $this->addPage($provider, $valueList);
        }

        return $this;
    }
}
