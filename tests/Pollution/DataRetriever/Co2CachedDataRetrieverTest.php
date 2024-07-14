<?php declare(strict_types=1);

namespace App\Tests\Pollution\DataRetriever;

use App\Air\Pollutant\PollutantInterface;
use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\DataCache\DataCacheInterface;
use App\Pollution\DataRetriever\Co2DataRetriever;
use App\Provider\NoaaProvider\NoaaProvider;
use App\Repository\StationRepository;
use Caldera\GeoBasic\Coord\Coord;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class Co2CachedDataRetrieverTest extends TestCase
{
    public function testCo2CachedDataRetriever(): void
    {
        $data = new Data();

        $station = $this->createStation();

        $dataCache = $this->createMock(DataCacheInterface::class);
        $dataCache
            ->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('luft-data-42-7'))
            ->willReturn($data);

        $stationRepository = $this->createMock(StationRepository::class);
        $stationRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneByStationCode'), $this->equalTo(['USHIMALO']))
            ->will($this->returnValue($station));

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Station::class))
            ->will($this->returnValue($stationRepository));

        $co2CacheDataRetriever = new Co2DataRetriever($dataCache, $registry);

        $coord = new Coord(57.3, 10.5);

        $resultDataList = $co2CacheDataRetriever->retrieveDataForCoord($coord, PollutantInterface::POLLUTANT_CO2);

        $this->assertCount(1, $resultDataList);
        $this->assertEquals([$data], $resultDataList);
    }

    public function testNoopIfNotCo2Requested(): void
    {
        $dataCache = $this->createMock(DataCacheInterface::class);
        $dataCache
            ->expects($this->never())
            ->method('getData');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->never())
            ->method('getRepository');

        $co2CacheDataRetriever = new Co2DataRetriever($dataCache, $registry);

        $coord = new Coord(57.3, 10.5);

        $resultDataList = $co2CacheDataRetriever->retrieveDataForCoord($coord, PollutantInterface::POLLUTANT_PM25);

        $this->assertCount(0, $resultDataList);
        $this->assertEquals([], $resultDataList);
    }

    public function testNoopIfStationIsRequested(): void
    {
        $dataCache = $this->createMock(DataCacheInterface::class);
        $dataCache
            ->expects($this->never())
            ->method('getData');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->never())
            ->method('getRepository');

        $co2CacheDataRetriever = new Co2DataRetriever($dataCache, $registry);

        $coord = new Station(57.3, 10.5);

        $resultDataList = $co2CacheDataRetriever->retrieveDataForCoord($coord, PollutantInterface::POLLUTANT_CO2);

        $this->assertCount(0, $resultDataList);
        $this->assertEquals([], $resultDataList);
    }

    protected function createStation(): Station
    {
        $station = new Station(19.536342, -155.576480);
        $station
            ->setId(42)
            ->setAltitude(3397)
            ->setStationCode('USHIMALO')
            ->setTitle('Mauna Loa Observatory')
            ->setProvider(NoaaProvider::IDENTIFIER);

        return $station;
    }
}