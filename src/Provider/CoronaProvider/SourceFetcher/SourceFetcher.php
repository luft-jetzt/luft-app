<?php declare(strict_types=1);

namespace App\Provider\CoronaProvider\SourceFetcher;

use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use GuzzleHttp\Client;

class SourceFetcher implements SourceFetcherInterface
{
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

        $response = (new Client())->get($url);

        return $response->getBody()->getContents();
    }
}
