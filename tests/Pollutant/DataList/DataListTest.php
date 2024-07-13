<?php declare(strict_types=1);

namespace App\Tests\Pollutant\DataList;

use App\Air\DataList\DataList;
use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class DataListTest extends TestCase
{
    public function testEmptyDataList(): void
    {
        $dataList = new DataList();

        $this->assertCount(7, $dataList->getList());
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_PM10]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_PM25]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_SO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_CO]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_NO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_CO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_O3]);
    }

    public function testAddData(): void
    {
        $dataList = new DataList();

        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM10));

        $this->assertCount(7, $dataList->getList());
        $this->assertCount(1, $dataList->getList()[MeasurementInterface::MEASUREMENT_PM10]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_PM25]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_SO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_CO]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_NO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_CO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_O3]);
    }

    public function testAddIdenticalData(): void
    {
        $dataList = new DataList();

        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM10));
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM10));

        $this->assertCount(7, $dataList->getList());
        $this->assertCount(1, $dataList->getList()[MeasurementInterface::MEASUREMENT_PM10]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_PM25]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_SO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_CO]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_NO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_CO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_O3]);
    }

    public function testAddNonIdenticallData(): void
    {
        $dataList = new DataList();

        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM10, 23.5));
        $dataList->addData($this->createData(MeasurementInterface::MEASUREMENT_PM10, 42.3));

        $this->assertCount(7, $dataList->getList());
        $this->assertCount(2, $dataList->getList()[MeasurementInterface::MEASUREMENT_PM10]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_PM25]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_SO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_CO]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_NO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_CO2]);
        $this->assertEmpty($dataList->getList()[MeasurementInterface::MEASUREMENT_O3]);
    }

    protected function createData(int $pollutant, float $value = 42.3, Station $station = null): Data
    {
        $data = new Data();
        $data
            ->setPollutant($pollutant)
            ->setDateTime(new \DateTime())
            ->setValue($value);

        if ($station) {
            $data->setStation($station);
        } else {
            $data->setStation($this->createStation());
        }

        return $data;
    }

    protected function createStation(string $stationCode = 'TESTSTATION', float $latitude = 57.1, float $longitude = 9.5): Station
    {
        $station = new Station($latitude, $longitude);
        $station->setStationCode($stationCode);

        return $station;
    }
}