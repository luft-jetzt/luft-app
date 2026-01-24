<?php declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\City;
use App\Entity\Network;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class StationTest extends TestCase
{
    public function testConstructor(): void
    {
        $station = new Station(52.520008, 13.404954);

        $this->assertEquals(52.520008, $station->getLatitude());
        $this->assertEquals(13.404954, $station->getLongitude());
        $this->assertStringContainsString('POINT', $station->getCoord());
    }

    public function testSettersAndGetters(): void
    {
        $station = new Station(52.520008, 13.404954);

        $station
            ->setId(42)
            ->setStationCode('DEBB021')
            ->setTitle('Berlin Neukölln')
            ->setAltitude(35)
            ->setProvider('uba')
            ->setStationType('background')
            ->setAreaType('urban');

        $this->assertEquals(42, $station->getId());
        $this->assertEquals('DEBB021', $station->getStationCode());
        $this->assertEquals('Berlin Neukölln', $station->getTitle());
        $this->assertEquals(35, $station->getAltitude());
        $this->assertEquals('uba', $station->getProvider());
        $this->assertEquals('background', $station->getStationType());
        $this->assertEquals('urban', $station->getAreaType());
    }

    public function testFromDateAndUntilDate(): void
    {
        $station = new Station(52.520008, 13.404954);

        $fromDate = new \DateTime('2020-01-01');
        $untilDate = new \DateTime('2025-12-31');

        $station
            ->setFromDate($fromDate)
            ->setUntilDate($untilDate);

        $this->assertEquals($fromDate, $station->getFromDate());
        $this->assertEquals($untilDate, $station->getUntilDate());
        $this->assertEquals('2020-01-01 00:00:00', $station->getFromDateFormatted());
        $this->assertEquals('2025-12-31 00:00:00', $station->getUntilDateFormatted());
    }

    public function testNullDatesReturnNull(): void
    {
        $station = new Station(52.520008, 13.404954);

        $this->assertNull($station->getFromDate());
        $this->assertNull($station->getUntilDate());
        $this->assertNull($station->getFromDateFormatted());
        $this->assertNull($station->getUntilDateFormatted());
    }

    public function testCity(): void
    {
        $station = new Station(52.520008, 13.404954);
        $city = $this->createMock(City::class);

        $station->setCity($city);

        $this->assertSame($city, $station->getCity());
    }

    public function testNetwork(): void
    {
        $station = new Station(52.520008, 13.404954);
        $network = $this->createMock(Network::class);

        $station->setNetwork($network);

        $this->assertSame($network, $station->getNetwork());
    }

    public function testUbaStationId(): void
    {
        $station = new Station(52.520008, 13.404954);

        $station->setUbaStationId(12345);

        $this->assertEquals(12345, $station->getUbaStationId());
    }

    public function testPin(): void
    {
        $station = new Station(52.520008, 13.404954);

        $this->assertEquals('52.520008,13.404954', $station->getPin());
    }

    public function testPrePersist(): void
    {
        $station = new Station(52.520008, 13.404954);
        $station->setLatitude(48.137154);
        $station->setLongitude(11.576124);

        $station->prePersist();

        $this->assertStringContainsString('POINT', $station->getCoord());
        $this->assertStringContainsString('11.576124', $station->getCoord());
        $this->assertStringContainsString('48.137154', $station->getCoord());
    }
}
