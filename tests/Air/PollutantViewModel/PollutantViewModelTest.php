<?php declare(strict_types=1);

namespace App\Tests\Air\PollutantViewModel;

use App\Air\Pollutant\CO;
use App\Air\ViewModel\PollutantViewModel;
use App\Entity\Data;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class PollutantViewModelTest extends TestCase
{
    public function testData(): void
    {
        $data = new Data();
        $viewModel = new PollutantViewModel($data);

        $this->assertEquals($data, $viewModel->getData());
    }

    public function testDataOverwritten(): void
    {
        $data = new Data();
        $data->setValue(10);
        $viewModel = new PollutantViewModel($data);

        $newData = new Data();
        $newData->setValue(20);
        $viewModel->setData($newData);

        $this->assertEquals($newData, $viewModel->getData());
        $this->assertNotEquals($data, $viewModel->getData());
    }

    public function testCaption(): void
    {
        $data = new Data();
        $viewModel = new PollutantViewModel($data);

        $viewModel->setCaption("Testcaption");

        $this->assertEquals("Testcaption", $viewModel->getCaption());
    }

    public function testDistance(): void
    {
        $data = new Data();
        $viewModel = new PollutantViewModel($data);

        $viewModel->setDistance(42.3);
        $this->assertEquals(42.3, $viewModel->getDistance());
    }

    public function testStation(): void
    {
        $data = new Data();
        $viewModel = new PollutantViewModel($data);

        $station = new Station(57.4, 10.3);
        $viewModel->setStation($station);

        $this->assertEquals($station, $viewModel->getStation());
    }

    public function testPollutant(): void
    {
        $data = new Data();
        $viewModel = new PollutantViewModel($data);

        $pollutant = new CO();
        $viewModel->setPollutant($pollutant);

        $this->assertEquals($pollutant, $viewModel->getPollutant());
    }
}
