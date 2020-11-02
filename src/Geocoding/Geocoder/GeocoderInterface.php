<?php declare(strict_types=1);

namespace App\Geocoding\Geocoder;

interface GeocoderInterface
{
    public function query(string $queryString): array;
}
