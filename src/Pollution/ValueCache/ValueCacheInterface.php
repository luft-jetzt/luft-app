<?php declare(strict_types=1);

namespace App\Pollution\ValueCache;

use App\Provider\ProviderInterface;

interface ValueCacheInterface
{
    public function setProvider(ProviderInterface $provider): ValueCacheInterface;
    public function getNewestPage(): array;
    public function addValuesToCache(array $valueList): ValueCacheInterface;
}
