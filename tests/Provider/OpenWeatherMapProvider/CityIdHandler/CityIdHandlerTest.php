<?php declare(strict_types=1);

namespace App\Tests\Provider\OpenWeatherMapProvider\CityIdHandler;

use App\Provider\OpenWeatherMapProvider\CityIdHandler\CityIdHandler;
use PHPUnit\Framework\TestCase;

class CityIdHandlerTest extends TestCase
{
    public function testItLoadsAndReturnsSomething(): void
    {
        $cityIdHandler = new CityIdHandler();

        $cityIdHandler->load();

        $this->assertNotNull($cityIdHandler->getJson());
    }
}