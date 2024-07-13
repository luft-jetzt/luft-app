<?php declare(strict_types=1);

namespace App\Tests\Air\MeasurementViewModel;

use App\Air\Pollutant\CO;
use App\Air\ViewModel\MeasurementViewModel;
use App\Entity\Data;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class MeasurementViewModelTest extends TestCase
{
    public function testData(): void
    {
        $data = new Data();
        $viewModel = new MeasurementViewModel($data);

        $this->assertEquals($data, $viewModel->getData());
    }

    public function testDataOverwritten(): void
    {
        $data = new Data();
        $data->setValue(10);
        $viewModel = new MeasurementViewModel($data);

        $newData = new Data();
        $newData->setValue(20);
        $viewModel->setData($newData);

        $this->assertEquals($newData, $viewModel->getData());
        $this->assertNotEquals($data, $viewModel->getData());
    }

    public function testCaption(): void
    {
        $data = new Data();
        $viewModel = new MeasurementViewModel($data);

        $viewModel->setCaption("Testcaption");

        $this->assertEquals("Testcaption", $viewModel->getCaption());
    }

    public function testDistance(): void
    {
        $data = new Data();
        $viewModel = new MeasurementViewModel($data);

        $viewModel->setDistance(42.3);
        $this->assertEquals(42.3, $viewModel->getDistance());
    }

    public function testStation(): void
    {
        $data = new Data();
        $viewModel = new MeasurementViewModel($data);

        $station = new Station(57.4, 10.3);
        $viewModel->setStation($station);

        $this->assertEquals($station, $viewModel->getStation());
    }

    public function testMeasurement(): void
    {
        $data = new Data();
        $viewModel = new MeasurementViewModel($data);

        $measurement = new CO();
        $viewModel->setMeasurement($measurement);

        $this->assertEquals($measurement, $viewModel->getMeasurement());
    }
}
