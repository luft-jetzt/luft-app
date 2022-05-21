<?php declare(strict_types=1);

namespace App\Tests\Pollutant\PollutantFactoryStrategy;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;
use App\Pollution\DataList\DataList;
use App\Pollution\PollutantFactoryStrategy\SimplePollutantFactoryStrategy;
use PHPUnit\Framework\TestCase;

class SimplePollutantFactoryStrategyTest extends TestCase
{
    /** @var int $testDataId */
    protected $testDataId = 0;

    public function testAllPollutantsAreMissingInitiallyForEmptyDataList(): void
    {
        $dataList = new DataList();

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_PM10,
            MeasurementInterface::MEASUREMENT_O3,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_SO2,
            MeasurementInterface::MEASUREMENT_CO,
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testMissingPollutantsAfterInsertingTwoData(): void
    {
        $dataList = new DataList();

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_CO));
        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_O3));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_PM10,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_SO2,
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testEmptyStrategyNotSatisfiedWithEmptyDataList(): void
    {
        $dataList = new DataList();

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_O3));
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_NO2));
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_SO2));
    }

    public function testEmptyStrategyNotSatisfiedWithPopulatedDataListForeignMeasurement(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_NO2));

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));
    }

    public function testStrategy(): void
    {
        $dataList = new DataList();

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_PM10,
            MeasurementInterface::MEASUREMENT_O3,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_SO2,
            MeasurementInterface::MEASUREMENT_CO,
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));

        // add SO2
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_SO2));

        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_SO2));

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_SO2));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_PM10,
            MeasurementInterface::MEASUREMENT_O3,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_CO,
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));

        // add PM10
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));

        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_PM10));

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_O3,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_CO,
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));

        // add O3
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_O3));

        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_O3));

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_O3));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_PM25,
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_CO,
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));

        // add PM25
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));

        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_PM25));

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_NO2,
            MeasurementInterface::MEASUREMENT_CO,
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));

        // add NO2
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_NO2));

        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_NO2));

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_NO2));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_CO,
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));

        // add CO
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));

        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_CO));

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));

        $expectedMissingPollutantsList = [
            MeasurementInterface::MEASUREMENT_CO2,
        ];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));

        // add CO2
        $this->assertFalse($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO2));

        $simplePollutantFactoryStrategy->addDataToList($dataList, $this->createData(MeasurementInterface::MEASUREMENT_CO2));

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO2));

        $expectedMissingPollutantsList = [];

        $this->assertEquals($expectedMissingPollutantsList, $simplePollutantFactoryStrategy->getMissingPollutants($dataList));
    }

    public function testStrategySatisfiedWithDataListContainingCO(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_CO));

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_CO));
    }

    public function testStrategySatisfiedWithDataListContainingSO2(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_SO2));

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_SO2));
    }

    public function testStrategySatisfiedWithDataListContainingO3(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_O3));

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_O3));
    }

    public function testStrategySatisfiedWithDataListContainingNO2(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_NO2));

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_NO2));
    }

    public function testStrategySatisfiedWithDataListContainingPM10(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM10));

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM10));
    }

    public function testStrategySatisfiedWithDataListContainingPM25(): void
    {
        $dataList = new DataList();
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM25));

        $simplePollutantFactoryStrategy = new SimplePollutantFactoryStrategy();

        $this->assertTrue($simplePollutantFactoryStrategy->isSatisfied($dataList, MeasurementInterface::MEASUREMENT_PM25));
    }

    protected function createData(int $measurementId, string $provider = 'test-provider'): Data
    {
        $station = new Station(53.4, 9.73);
        $station
            ->setProvider($provider)
            ->setStationCode('DESH001');

        $data = new Data();
        $data
            ->setId(++$this->testDataId)
            ->setPollutant($measurementId)
            ->setStation($station)
            ->setDateTime(new \DateTime('2019-01-01 12:34:56'))
            ->setValue(42.3);

        return $data;
    }
}
