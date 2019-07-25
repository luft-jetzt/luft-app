<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\CityIdHandler;

use App\Entity\City;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CityIdHandler
{
    /** @var array $cityDataList */
    protected $cityDataList;

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var CityDataLoader $cityDataLoader */
    protected $cityDataLoader;

    /** @var array $cityList */
    protected $cityList = [];

    public function __construct(RegistryInterface $registry, CityDataLoader $cityDataLoader)
    {
        $this->registry = $registry;
        $this->cityDataLoader = $cityDataLoader;
    }

    public function assign(): void
    {
        $this->cityDataList = $this->cityDataLoader->loadCityData();

        $this->cityList = $this->registry->getRepository(City::class)->findAll();
    }
}
