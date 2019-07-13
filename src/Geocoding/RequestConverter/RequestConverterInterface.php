<?php declare(strict_types=1);

namespace App\Geocoding\RequestConverter;

use App\Geocoding\Query\GeoQueryInterface;
use Caldera\GeoBasic\Coord\Coord;
use Symfony\Component\HttpFoundation\Request;

interface RequestConverterInterface
{
    public function getCoordByRequest(Request $request): ?Coord;
}
