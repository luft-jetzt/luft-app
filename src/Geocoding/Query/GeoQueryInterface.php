<?php declare(strict_types=1);

namespace App\Geocoding\Query;

interface GeoQueryInterface
{
    public function query(string $queryString): array;
}
