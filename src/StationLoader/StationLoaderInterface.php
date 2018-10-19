<?php declare(strict_types=1);

namespace App\StationLoader;

interface StationLoaderInterface
{
    public function load(): array;
}
