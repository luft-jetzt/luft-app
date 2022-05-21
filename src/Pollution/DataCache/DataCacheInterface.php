<?php declare(strict_types=1);

namespace App\Pollution\DataCache;

use App\Entity\Data;

interface DataCacheInterface
{
    public function addData(Data $data): DataCacheInterface;

    public function getData(string $key): ?Data;
}
