<?php declare(strict_types=1);

namespace App\Air\Provider\OpenWeatherMapProvider\SourceFetcher;

use App\Air\SourceFetcher\FetchProcess;
use App\Air\SourceFetcher\FetchResult;
use App\Air\SourceFetcher\SourceFetcherInterface;
use Caldera\GeoBasic\Coord\CoordInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SourceFetcher implements SourceFetcherInterface
{
    private const float TIMEOUT = 10.0;

    public function __construct(protected string $openWeatherMapAppId, protected HttpClientInterface $httpClient)
    {
    }

    #[\Override]
    public function fetch(FetchProcess $fetchProcess): FetchResult
    {
        $fetchResult = new FetchResult();

        if (array_key_exists('uvindex', $fetchProcess->getPollutantList()) && $fetchProcess->getCoord()) {
            $this->queryUVIndex($fetchProcess->getCoord());

            $fetchResult->incCounter('uvindex');
        }

        if (array_key_exists('temperature', $fetchProcess->getPollutantList()) && $fetchProcess->getCoord()) {
            $this->queryTemperature($fetchProcess->getCoord());

            $fetchResult->incCounter('temperature');
        }

        return $fetchResult;
    }

    public function queryUVIndex(CoordInterface $coord): string
    {
        $url = sprintf('https://api.openweathermap.org/data/2.5/uvi?units=metric&lat=%f&lon=%f&appid=%s', $coord->getLatitude(), $coord->getLongitude(), $this->openWeatherMapAppId);

        return $this->httpClient->request('GET', $url, ['timeout' => self::TIMEOUT])->getContent();
    }

    public function queryTemperature(CoordInterface $coord): string
    {
        $url = sprintf('https://api.openweathermap.org/data/2.5/weather?units=metric&lat=%f&lon=%f&appid=%s', $coord->getLatitude(), $coord->getLongitude(), $this->openWeatherMapAppId);

        return $this->httpClient->request('GET', $url, ['timeout' => self::TIMEOUT])->getContent();
    }
}
