<?php declare(strict_types=1);

namespace App\Tests\Pollutant\PollutantFactoryStrategy;

use App\Air\DataList\DataList;
use App\Air\Pollutant\PollutantInterface;
use App\Air\PollutantFactoryStrategy\LuftdatenPollutantFactoryStrategy;
use App\Entity\Data;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class LuftdatenPollutantFactoryStrategyTest extends TestCase
{
    /** @var int $testDataId */
    protected $testDataId = 0;

    public function testAllPollutantsAreMissingInitiallyForEmptyDataList(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_PM25,
            PollutantInterface::POLLUTANT_PM10,
            PollutantInterface::POLLUTANT_O3,
            PollutantInterface::POLLUTANT_NO2,
            PollutantInterface::POLLUTANT_SO2,
            PollutantInterface::POLLUTANT_CO,
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testMissingPollutantsAfterInsertingTwoData(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_CO));
        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_O3));

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_PM25,
            PollutantInterface::POLLUTANT_PM10,
            PollutantInterface::POLLUTANT_NO2,
            PollutantInterface::POLLUTANT_SO2,
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testEmptyStrategyNotSatisfiedWithEmptyDataList(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM25));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM10));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_CO));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_O3));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_NO2));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_SO2));
    }

    public function testEmptyStrategyNotSatisfiedWithPopulatedDataListForeignPollutant(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_NO2));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_CO));
    }

    public function testStrategy(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_PM25,
            PollutantInterface::POLLUTANT_PM10,
            PollutantInterface::POLLUTANT_O3,
            PollutantInterface::POLLUTANT_NO2,
            PollutantInterface::POLLUTANT_SO2,
            PollutantInterface::POLLUTANT_CO,
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add SO2
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_SO2));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_SO2));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_SO2));

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_PM25,
            PollutantInterface::POLLUTANT_PM10,
            PollutantInterface::POLLUTANT_O3,
            PollutantInterface::POLLUTANT_NO2,
            PollutantInterface::POLLUTANT_CO,
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add PM10
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM10));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_PM10));

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM10));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_PM10, 'foo-provider', 'DESH002'));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM10));

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_PM25,
            PollutantInterface::POLLUTANT_O3,
            PollutantInterface::POLLUTANT_NO2,
            PollutantInterface::POLLUTANT_CO,
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add O3
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_O3));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_O3));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_O3));

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_PM25,
            PollutantInterface::POLLUTANT_NO2,
            PollutantInterface::POLLUTANT_CO,
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add PM25
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM25));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_PM25));

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM25));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_PM25, 'foo-provider', 'DESH002'));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM25));

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_NO2,
            PollutantInterface::POLLUTANT_CO,
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add NO2
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_NO2));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_NO2));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_NO2));

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_CO,
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add CO
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_CO));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_CO));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_CO));

        $expectedMissingPollutantsList = [
            PollutantInterface::POLLUTANT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add CO
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_CO2));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(PollutantInterface::POLLUTANT_CO2));

        $expectedMissingPollutantsList = [
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testStrategySatisfiedWithDataListContainingCO(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_CO));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_CO));
    }

    public function testStrategySatisfiedWithDataListContainingSO2(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_SO2));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_SO2));
    }

    public function testStrategySatisfiedWithDataListContainingO3(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_O3));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_O3));
    }

    public function testStrategySatisfiedWithDataListContainingNO2(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_NO2));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_NO2));
    }

    public function testStrategySatisfiedWithDataListContainingTwoPM10(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_PM10));

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM10));

        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_PM10, 'foo-provider', 'DESH002'));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM10));
    }

    public function testStrategySatisfiedWithDataListContainingTwoPM25(): void
    {
        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $dataList = new DataList();

        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_PM25));

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM25));

        $dataList->addData($this->createData(PollutantInterface::POLLUTANT_PM25, 'foo-provider', 'DESH002'));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, PollutantInterface::POLLUTANT_PM25));
    }

    protected function createData(int $pollutantId, string $provider = 'test-provider', string $stationCode = 'DESH001'): Data
    {
        $station = new Station(53.4, 9.73);
        $station
            ->setProvider($provider)
            ->setStationCode($stationCode);

        $data = new Data();
        $data
            ->setId(++$this->testDataId)
            ->setPollutant($pollutantId)
            ->setStation($station)
            ->setDateTime(new \DateTime('2019-01-01 12:34:56'))
            ->setValue(42.3);

        return $data;
    }
}
