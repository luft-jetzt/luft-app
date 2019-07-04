<?php declare(strict_types=1);

namespace App\Tests\Air\MeasurementList;

use App\Air\Measurement\CO;
use App\Air\Measurement\NO2;
use App\Air\Measurement\O3;
use App\Air\Measurement\SO2;
use App\Air\MeasurementList\MeasurementList;
use PHPUnit\Framework\TestCase;

class MeasurementListTest extends TestCase
{
    public function testEmptyList(): void
    {
        $this->assertCount(0, (new MeasurementList())->getMeasurements());
    }

    public function testListWithTwoElements(): void
    {
        $measurementList = new MeasurementList();

        $measurementList
            ->addMeasurement(new CO())
            ->addMeasurement(new NO2());

        $this->assertCount(2, $measurementList->getMeasurements());
    }

    public function testListContentWithTwoElements(): void
    {
        $measurementList = new MeasurementList();

        $measurementList
            ->addMeasurement(new CO())
            ->addMeasurement(new NO2());

        $actualMeasurementList = $measurementList->getMeasurements();
        $expectedMeasurementList = [
            new CO(),
            new NO2(),
        ];

        $this->assertEquals($expectedMeasurementList, $actualMeasurementList);
    }

    public function testListWithIds(): void
    {
        $measurementList = new MeasurementList();

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