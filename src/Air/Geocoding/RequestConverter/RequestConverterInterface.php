<?php declare(strict_types=1);

namespace App\Air\Geocoding\RequestConverter;

use App\Geo\Coordinate\CoordinateInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestConverterInterface
{
    public function getCoordByRequest(Request $request): ?CoordinateInterface;
}
