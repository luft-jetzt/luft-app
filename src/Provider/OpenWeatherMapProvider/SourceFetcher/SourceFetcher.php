<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\SourceFetcher;

use Caldera\GeoBasic\Coord\CoordInterface;
use Curl\Curl;

class SourceFetcher
{
    /** @var Curl $curl */
    protected $curl;

    /** @var string $openWeatherAppId */
    protected $openWeatherMapAppId;

    public function __construct(string $openWeatherMapAppId)
    {
        $this->curl = new Curl();
        $this->openWeatherMapAppId = $openWeatherMapAppId;
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
