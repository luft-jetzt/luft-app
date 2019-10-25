<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher;

use App\Provider\UmweltbundesamtDe\SourceFetcher\Query\UbaQueryInterface;
use App\Provider\UmweltbundesamtDe\SourceFetcher\QueryBuilder\QueryBuilder;
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
        $data = QueryBuilder::buildQueryString($query);

        $queryString = sprintf('https://www.umweltbundesamt.de/api/air_data/v2/measures/json?%s', $data);

        $this->curl->get($queryString);


dump($queryString);die;
        return $this->curl->response;
    }
}
