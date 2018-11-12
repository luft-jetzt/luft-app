<?php declare(strict_types=1);

namespace App\Pollution\ValueCache;

use App\Provider\ProviderInterface;

interface ValueCacheInterface
{
    public function addValuesToCache(ProviderInterface $provider, array $valueList): ValueCacheInterface;
}
