<?php declare(strict_types=1);

namespace App\Provider\EuropeanEnvironmentAgency\SourceFetcher\Loader;

use Curl\Curl;

abstract class AbstractLoader
{
    /** @var string */
    const BASE_URL = 'http://discomap.eea.europa.eu/map/fme/latest/%s_%s.csv';

    /** @var Curl $curl */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }
}
