<?php declare(strict_types=1);

namespace App\Tests\Pollutant\ViewModel;

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

        $pollutantViewModel = new PollutantViewModel($data);

        $this->assertEquals($data, $pollutantViewModel->getData());
    }

    public function testDistance(): void
    {
        $data = new Data();

        $pollutantViewModel = new PollutantViewModel($data);
        $pollutantViewModel->setDistance(42.5);

        $this->assertEquals(42.5, $pollutantViewModel->getDistance());
    }

    public function testCaption(): void
    {
        $data = new Data();

        $pollutantViewModel = new PollutantViewModel($data);
        $pollutantViewModel->setCaption('Testcaption');

        $this->assertEquals('Testcaption', $pollutantViewModel->getCaption());
    }

    public function testPollutant(): void
    {
        $data = new Data();
        $pollutant = new CO();

        $pollutantViewModel = new PollutantViewModel($data);
        $pollutantViewModel->setPollutant($pollutant);

        $this->assertEquals($pollutant, $pollutantViewModel->getPollutant());
    }

    public function testPollutionLevel(): void
    {
        $data = new Data();

        $pollutantViewModel = new PollutantViewModel($data);
        $pollutantViewModel->setPollutionLevel(2);

        $this->assertEquals(2, $pollutantViewModel->getPollutionLevel());
    }

    public function testStation(): void
    {
        $data = new Data();
        $station = new Station(53.5, 10.2);

        $pollutantViewModel = new PollutantViewModel($data);
        $pollutantViewModel->setStation($station);

        $this->assertEquals($station, $pollutantViewModel->getStation());
    }
}
