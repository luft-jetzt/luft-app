<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\CityIdHandler;

class CityIdHandler
{
    protected $json;

    public function load(): void
    {
        $this->json = json_decode(file_get_contents( __DIR__.'/../../../../public/city.list.json'));
    }

    public function getJson(): array
    {
        return $this->json;
    }
}