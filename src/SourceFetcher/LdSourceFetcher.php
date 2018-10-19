<?php declare(strict_types=1);

namespace App\SourceFetcher;

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
