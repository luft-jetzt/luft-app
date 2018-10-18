<?php declare(strict_types=1);

namespace App\Geocoding;

use Geocoder\Geocoder;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\StatefulGeocoder;
use Http\Adapter\Guzzle6\Client;

abstract class AbstractGeocoding
{
    const LOCALE = 'de';
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org';
    const USER_AGENT = 'Luft.jetzt geocoder';
    const REFERER = 'https://luft.jetzt/';

    /** @var Geocoder $geocoder */
    protected $geocoder;

    public function __construct()
    {
        $httpClient = new Client();
        $provider = new Nominatim($httpClient, self::NOMINATIM_URL, self::USER_AGENT, self::REFERER);
        $this->geocoder = new StatefulGeocoder($provider, self::LOCALE);
    }
}
