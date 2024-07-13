<?php declare(strict_types=1);

namespace App\Tests\Air\MeasurementList;

use App\Air\Pollutant\CO;
use App\Air\Pollutant\NO2;
use App\Air\Pollutant\O3;
use App\Air\Pollutant\SO2;
use App\Air\PollutantList\PollutantList;
use PHPUnit\Framework\TestCase;

class MeasurementListTest extends TestCase
{
    public function testEmptyList(): void
    {
        $this->assertCount(0, (new PollutantList())->getMeasurements());
    }

    public function testListWithTwoElements(): void
    {
        $measurementList = new PollutantList();

        $measurementList
            ->addMeasurement(new CO())
            ->addMeasurement(new NO2());

        $this->assertCount(2, $measurementList->getMeasurements());
    }

    public function testListContentWithTwoElements(): void
    {
        $measurementList = new PollutantList();

        $measurementList
            ->addMeasurement(new CO())
            ->addMeasurement(new NO2());

        $actualMeasurementList = $measurementList->getMeasurements();
        $expectedMeasurementList = [
            'co' => new CO(),
            'no2' => new NO2(),
        ];

        $this->assertEquals($expectedMeasurementList, $actualMeasurementList);
    }

    public function testListWithIds(): void
    {
        $measurementList = new PollutantList();

        $measurementList
            ->addMeasurement(new O3())
            ->addMeasurement(new SO2());

        $actualMeasurementList = $measurementList->getMeasurements();
        $expectedMeasurementList = [
            'o3' => new O3(),
            'so2' => new SO2(),
        ];

        $this->assertEquals($expectedMeasurementList, $actualMeasurementList);
    }
}