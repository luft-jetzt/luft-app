<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\CityIdHandler;

class CityDataLoader
{
    public function loadCityData(): array
    {
        return json_decode(file_get_contents( __DIR__.'/../../../../public/city.list.json'));
    }
}
