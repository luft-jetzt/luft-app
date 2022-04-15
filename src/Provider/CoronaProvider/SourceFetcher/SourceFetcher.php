<?php declare(strict_types=1);

namespace App\Provider\CoronaProvider\SourceFetcher;

use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Curl\Curl;

class SourceFetcher implements SourceFetcherInterface
{
    protected Curl $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    public function fetch(FetchProcess $fetchProcess): FetchResult
    {
        $fetchResult = new FetchResult();

        if (array_key_exists('coronaincidence', $fetchProcess->getMeasurementList()) && $fetchProcess->getCoord()) {
            $this->queryCoronaIncidence($fetchProcess->getCoord());

            $fetchResult->incCounter('coronaincidence');
        }

        return $fetchResult;
    }

    public function queryCoronaIncidence(CoordInterface $coord): string
    {
        $url = sprintf('https://corona.criticalmass.in?latitude=%f&longitude=%f', $coord->getLatitude(), $coord->getLongitude());
        $this->curl->get($url);

        return $this->curl->rawResponse;
    }
}
