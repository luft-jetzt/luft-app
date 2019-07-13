<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher;

use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaQueryInterface;
use Curl\Curl;

class SourceFetcher
{
    /** @var Curl $curl */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function query(UbaQueryInterface $query): \stdClass
    {
        $queryString = sprintf('https://www.umweltbundesamt.de/js/uaq/data/stations/measuring?%s', $query->getQueryString());

        $this->curl->get($queryString);

        return $this->curl->response;
    }
}
