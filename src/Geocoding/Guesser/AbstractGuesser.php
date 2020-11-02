<?php declare(strict_types=1);

namespace App\Geocoding\Guesser;

use Geocoder\Provider\Provider;

abstract class AbstractGuesser
{
    protected Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }
}
