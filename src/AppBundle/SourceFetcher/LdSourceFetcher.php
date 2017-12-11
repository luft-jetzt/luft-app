<?php

namespace AppBundle\SourceFetcher;

use AppBundle\SourceFetcher\Query\QueryInterface;
use Curl\Curl;

class LdSourceFetcher implements SourceFetcherInterface
{
    public function query(QueryInterface $query = null): string
    {
        $curl = new Curl();

        $queryString = 'https://api.luftdaten.info/static/v2/data.dust.min.json';

        $curl->get($queryString);

        return $curl->response;
    }
}
