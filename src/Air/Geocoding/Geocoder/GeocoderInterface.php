<?php declare(strict_types=1);

namespace App\Air\Geocoding\Geocoder;

use Geocoder\Model\AddressCollection;

interface GeocoderInterface
{
    public function query(string $queryString): array;
    public function queryZip(string $zipCode): AddressCollection;
}
