<?php declare(strict_types=1);

namespace App\Pollution\ValueCache;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class ValueCache implements ValueCacheInterface
{
    /** @var AbstractAdapter $cache */
    protected $cache;

    public function __construct()
    {
        $this->cache = $this->createConnection();
    }

    protected function createConnection(): AbstractAdapter
    {
        $client = RedisAdapter::createConnection('redis://localhost');

        $cache = new RedisAdapter($client);

        return $cache;
    }

    protected function getPagination(): array
    {
        $key = sprintf('values-list');

        $listItem = $this->cache->getItem($key);

        return $listItem->get() ?? [];
    }

    protected function savePagination(array $pagination): ValueCache
    {
        $key = sprintf('values-list');

        $listItem = $this->cache->getItem($key);

        $listItem->set($pagination);

        $this->cache->save($listItem);

        return $this;
    }

    protected function addPage(array $valueList): int
    {
        $pagination = $this->getPagination();

        $nextPage = 0 === count($pagination) ? 1 : max(array_keys($pagination)) + 1;

        $pagination[$nextPage] = count($valueList);

        $this->savePagination($pagination);

        $key = sprintf('values-%d', $nextPage);

        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($valueList);
        $this->cache->save($cacheItem);

        return $nextPage;
    }

    protected function getPage(int $pageNumber): array
    {
        $key = sprintf('values-%d', $pageNumber);

        $pageItem = $this->cache->getItem($key);

        return $pageItem->get() ?? [];
    }

    public function getNewestPage(): array
    {
        $pagination = $this->getPagination();

        if (!$pagination) {
            return [];
        }

        $pageNumber = max(array_keys($pagination));

        unset($pagination[$pageNumber]);

        $this->savePagination($pagination);

        $page = $this->getPage($pageNumber);

        return $page;
    }

    public function addValuesToCache(array $valueList): ValueCacheInterface
    {
        if (0 !== count($valueList)) {
            $this->addPage($valueList);
        }

        return $this;
    }
}
