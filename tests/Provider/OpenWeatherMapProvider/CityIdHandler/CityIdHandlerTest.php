<?php declare(strict_types=1);

namespace App\Tests\Provider\OpenWeatherMapProvider\CityIdHandler;

use App\Entity\City;
use App\Provider\OpenWeatherMapProvider\CityIdHandler\CityDataLoader;
use App\Provider\OpenWeatherMapProvider\CityIdHandler\CityIdHandler;
use App\Repository\CityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CityIdHandlerTest extends TestCase
{
    public function testCalls(): void
    {
        $cityRepository = $this->createMock(CityRepository::class);
        $cityRepository
            ->expects($this->once())
            ->method('findAll')
            ->with()
            ->will($this->returnValue([]));

        $cityDataLoader = $this->createMock(CityDataLoader::class);
        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(City::class))
            ->will($this->returnValue($cityRepository));

        $cityIdHandler = new CityIdHandler($registry, $cityDataLoader);
        $cityIdHandler->assign();
    }

    public function testHamburg(): void
    {
        $mockedCityDataList = [
            [
                'id' => 2911297,
                'name' => 'Freie und Hansestadt Hamburg',
                'country' => 'DE',
                'coord' => [
                    'lon' => 10,
                    'lat' => 53.583328,
                ]
            ],
            [
                'id' => 2956829,
                'name' => 'Altstadt',
                'country' => 'DE',
                'coord' => [
                    'lon' => 10,
                    'lat' => 53.549999,
                ]
            ],
            [
                'id' => 4627266,
                'name' => 'Hamburg',
                'country' => 'US',
                'coord' => [
                    'lon' => -88.304207,
                    'lat' => 35.09481,
                ]
            ],
        ];

        $mockedCity = new City();
        $mockedCity
            ->setName('Hamburg');

        $mockedCityList = [$mockedCity];

        $cityRepository = $this->createMock(CityRepository::class);
        $cityRepository
            ->expects($this->once())
            ->method('findAll')
            ->with()
            ->will($this->returnValue($mockedCityList));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(City::class))
            ->will($this->returnValue($cityRepository));

        $cityDataLoader = $this->createMock(CityDataLoader::class);
        $cityDataLoader
            ->expects($this->once())
            ->method('loadCityData')
            ->will($this->returnValue($mockedCityDataList));

        $cityIdHandler = new CityIdHandler($registry, $cityDataLoader);
        $cityIdHandler->assign();
    }
}