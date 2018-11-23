<?php declare(strict_types=1);

namespace App\Pollution\ValueCache;

interface ValueCacheInterface
{
    public function getNewestPage(): array;
    public function addValuesToCache(array $valueList): ValueCacheInterface;
}
