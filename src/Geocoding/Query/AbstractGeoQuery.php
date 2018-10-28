<?php declare(strict_types=1);

namespace App\Geocoding\Query;

use Curl\Curl;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractGeoQuery implements GeoQueryInterface
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var Curl $curl */
    protected $curl;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->curl = new Curl();
    }
}
