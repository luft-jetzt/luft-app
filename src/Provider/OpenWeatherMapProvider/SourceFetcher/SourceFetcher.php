<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\SourceFetcher;

use App\SourceFetcher\FetchProcess;
use App\SourceFetcher\FetchResult;
use App\SourceFetcher\SourceFetcherInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Curl\Curl;

class SourceFetcher implements SourceFetcherInterface
{
    protected Curl $curl;

    protected string $openWeatherMapAppId;

    public function __construct(string $openWeatherMapAppId)
    {
        $this->curl = new Curl();
        $this->openWeatherMapAppId = $openWeatherMapAppId;
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
        $url = sprintf('https://api.openweathermap.org/data/2.5/uvi?units=metric&lat=%f&lon=%f&appid=%s', $coord->getLatitude(), $coord->getLongitude(), $this->openWeatherMapAppId);
        $this->curl->get($url);

        return $this->curl->rawResponse;
    }

    public function queryTemperature(CoordInterface $coord): string
    {
        $url = sprintf('https://api.openweathermap.org/data/2.5/weather?units=metric&lat=%f&lon=%f&appid=%s', $coord->getLatitude(), $coord->getLongitude(), $this->openWeatherMapAppId);
        $this->curl->get($url);

        return $this->curl->rawResponse;
    }
}
