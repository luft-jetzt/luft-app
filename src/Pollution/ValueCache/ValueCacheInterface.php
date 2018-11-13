<?php declare(strict_types=1);

namespace App\Pollution\ValueCache;

use App\Provider\ProviderInterface;

interface ValueCacheInterface
{
    public function setProvider(ProviderInterface $provider): ValueCacheInterface;
    public function getNewestPage(ProviderInterface $provider): array;
    public function addValuesToCache(ProviderInterface $provider, array $valueList): ValueCacheInterface;
}
