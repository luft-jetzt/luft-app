<?php declare(strict_types=1);

namespace App\Air\Geocoding\Geocoder;

use Geocoder\Provider\Provider;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractGeocoder implements GeocoderInterface
{
    public function __construct(protected RouterInterface $router, protected Provider $provider)
    {
    }
}
