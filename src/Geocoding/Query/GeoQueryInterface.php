<?php declare(strict_types=1);

namespace App\Geocoding\Query;

interface GeoQueryInterface
{
    public const QUERY_ADRESS = 'https://photon.komoot.de/api/?q=%s&lang=de';

    public function query(string $queryString): array;
}
