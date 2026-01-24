<?php declare(strict_types=1);

namespace App\Tests\Air\ViewModel;

use App\Air\Pollutant\PM10;
use App\Air\Pollutant\Temperature;
use App\Air\ViewModel\PollutantViewModel;
use App\Entity\Data;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class PollutantViewModelTest extends TestCase
{
    public function testConstructorWithData(): void
    {
        $data = new Data();
        $data->setValue(42.5);

        $viewModel = new PollutantViewModel($data);

        $this->assertSame($data, $viewModel->getData());
    }

    public function testSettersAndGetters(): void
    {
        $data = new Data();
        $station = new Station(52.520008, 13.404954);
        $pollutant = new PM10();

        $viewModel = new PollutantViewModel($data);
        $viewModel
            ->setStation($station)
            ->setPollutant($pollutant)
            ->setPollutionLevel(3)
            ->setCaption('Moderate')
            ->setDistance(1.5);

        $this->assertSame($station, $viewModel->getStation());
        $this->assertSame($pollutant, $viewModel->getPollutant());
        $this->assertEquals(3, $viewModel->getPollutionLevel());
        $this->assertEquals('Moderate', $viewModel->getCaption());
        $this->assertEquals(1.5, $viewModel->getDistance());
    }

    public function testShowOnMapDelegatesToPollutant(): void
    {
        $data = new Data();
        $viewModel = new PollutantViewModel($data);

        // PM10 should show on map
        $viewModel->setPollutant(new PM10());
        $this->assertTrue($viewModel->showOnMap());

        // Temperature should not show on map
        $viewModel->setPollutant(new Temperature());
        $this->assertFalse($viewModel->showOnMap());
    }

    public function testFluentInterface(): void
    {
        $data = new Data();
        $viewModel = new PollutantViewModel($data);

        $result = $viewModel
            ->setData($data)
            ->setStation(new Station(52.0, 13.0))
            ->setPollutant(new PM10())
            ->setPollutionLevel(1)
            ->setCaption('Good')
            ->setDistance(0.5);

        $this->assertSame($viewModel, $result);
    }
}
