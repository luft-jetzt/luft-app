<?php

namespace AppBundle\SourceFetcher;

use AppBundle\SourceFetcher\Query\QueryInterface;
use Curl\Curl;

class SourceFetcher
{
    public function query(QueryInterface $query)
    {
        $curl = new Curl();

        $queryString = 'https://www.umweltbundesamt.de/uaq/csv/stations/data?' . $query->getQueryString();

        echo $queryString;
        $curl->get($queryString);
        var_dump($curl->response);
    }
}