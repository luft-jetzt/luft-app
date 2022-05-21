<?php declare(strict_types=1);

namespace App\Geocoding\RequestConverter;

use Caldera\GeoBasic\Coordinate\CoordinateInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestConverterInterface
{
    public function getCoordByRequest(Request $request): ?CoordinateInterface;
}
