<?php declare(strict_types=1);

namespace App\Tests\Pollutant\PollutantFactoryStrategy;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\DataList\DataList;
use App\Pollution\PollutantFactoryStrategy\LuftdatenPollutantFactoryStrategy;
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
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_PM10,
            MeasurementInterface::MEASUREMENT_O3,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_SO2,
            MeasurementInterface::MEASUREMENT_CO,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testMissingPollutantsAfterInsertingTwoData(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_CO));
        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_O3));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_PM10,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_SO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testEmptyStrategyNotSatisfiedWithEmptyDataList(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_O3));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_NO2));
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_SO2));
    }

    public function testEmptyStrategyNotSatisfiedWithPopulatedDataListForeignMeasurement(): void
    {
        $data = new Data();
        $data->setPollutant(MeasurementInterface::MEASUREMENT_NO2);

        $dataList = new DataList();
        $dataList->addData($data);

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));
    }

    public function testStrategy(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_PM10,
            MeasurementInterface::MEASUREMENT_O3,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_SO2,
            MeasurementInterface::MEASUREMENT_CO,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add SO2
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_SO2));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_SO2));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_SO2));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_PM10,
            MeasurementInterface::MEASUREMENT_O3,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_CO,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add PM10
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_PM10));

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_PM10, 'foo-provider'));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_O3,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_CO,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add O3
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_O3));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_O3));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_O3));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_CO,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add PM25
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_PM25));

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_PM25, 'foo-provider'));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_CO,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add NO2
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_NO2));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_NO2));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_NO2));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_CO,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));

        // add CO
        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));

        $luftdatenPollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_CO));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));

        $expectedMissingPollutantsList = [];

        $this->assertEquals($expectedMissingPollutantsList, $luftdatenPollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testStrategySatisfiedWithDataListContainingCO(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_CO));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));
    }

    public function testStrategySatisfiedWithDataListContainingSO2(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_SO2));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_SO2));
    }

    public function testStrategySatisfiedWithDataListContainingO3(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_O3));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_O3));
    }

    public function testStrategySatisfiedWithDataListContainingNO2(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_NO2));

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_NO2));
    }

    public function testStrategySatisfiedWithDataListContainingTwoPM10(): void
    {
        $dataList = new DataList();

        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM10));

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));

        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM10, 'foo-provider'));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));
    }

    public function testStrategySatisfiedWithDataListContainingTwoPM25(): void
    {
        $luftdatenPollutantFactoryStrategy = new LuftdatenPollutantFactoryStrategy();

        $dataList = new DataList();

        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM25));

        $this->assertFalse($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));

        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM25, 'foo-provider'));

        $this->assertTrue($luftdatenPollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));
    }

    protected function createData(int $measurementId, string $provider = 'test-provider'): Data
    {
        $station = new Station(53.4, 9.73);
        $station->setProvider($provider);

        $data = new Data();
        $data
            ->setId(++$this->testDataId)
            ->setPollutant($measurementId)
            ->setStation($station);

        return $data;
    }
}
