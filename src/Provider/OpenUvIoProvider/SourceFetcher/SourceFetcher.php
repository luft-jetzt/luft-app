<?php declare(strict_types=1);

namespace App\Provider\OpenUvIoProvider\SourceFetcher;

use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use GuzzleHttp\Client;

class SourceFetcher implements SourceFetcherInterface
{
    public function __construct(protected string $openWeatherMapAppId)
    {
    }

    public function fetch(FetchProcess $fetchProcess): FetchResult
    {
        $fetchResult = new FetchResult();

        if (array_key_exists('uvindex', $fetchProcess->getMeasurementList()) && $fetchProcess->getCoord()) {
            $this->queryUVIndex($fetchProcess->getCoord());

            $fetchResult->incCounter('uvindex');
        }

        if (array_key_exists('temperature', $fetchProcess->getMeasurementList()) && $fetchProcess->getCoord()) {
            $this->queryTemperature($fetchProcess->getCoord());

            $fetchResult->incCounter('temperature');
        }

        return $fetchResult;
    }

    public function queryUVIndex(CoordInterface $coord): string
    {
        $url = sprintf('https://api.openuv.io/api/v1/uv?lat=%f&lng=%f', $coord->getLatitude(), $coord->getLongitude());

        $response = (new Client())->get($url, [
            'headers' => [
                'x-access-token' => 'openuv-4l5s5rliqjkb7v-io',
            ]
        ]);

        dd($response);
        return $response->getBody()->getContents();
    }
}
