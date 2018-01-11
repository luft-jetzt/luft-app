<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher;

use AppBundle\SourceFetcher\Query\QueryInterface;
use Curl\Curl;

class SourceFetcher
{
    public function query(QueryInterface $query): string
    {
        $curl = new Curl();

        $queryString = 'https://www.umweltbundesamt.de/uaq/csv/stations/data?' . $query->getQueryString();

        $curl->get($queryString);

        return (string) $curl->response;
    }
}
