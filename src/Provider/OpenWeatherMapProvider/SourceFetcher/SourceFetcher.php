<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\SourceFetcher;

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

    public function query(float $latitude, float $longitude): string
    {
        $url = sprintf('https://api.openweathermap.org/data/2.5/uvi?lat=%f&lon=%f&appid=%s', $latitude, $longitude, $this->openWeatherMapAppId);
        $this->curl->get($url);

        return $this->curl->response;
    }
}
