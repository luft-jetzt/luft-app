<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher;

use AppBundle\SourceFetcher\Query\QueryInterface;
use Curl\Curl;

class SourceFetcher
{
    /** @var Curl $curl */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function query(QueryInterface $query): string
    {
        $queryString = sprintf('https://www.umweltbundesamt.de/uaq/csv/stations/data?%s', $query->getQueryString());

        $this->curl->get($queryString);

        return (string) $this->curl->response;
    }
}
