<?php declare(strict_types=1);

namespace App\Geocoding\Geocoder;

use Geocoder\Provider\Provider;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractGeocoder implements GeocoderInterface
{
    protected RouterInterface $router;

    protected Provider $provider;

    public function __construct(RouterInterface $router, Provider $provider)
    {
        $this->router = $router;
        $this->provider = $provider;
    }
}
