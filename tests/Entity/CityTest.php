<?php declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\City;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class CityTest extends TestCase
{
    public function testConstructorSetsCreatedAt(): void
    {
        $before = new \DateTime();
        $city = new City();
        $after = new \DateTime();

        $this->assertInstanceOf(\DateTime::class, $city->getCreatedAt());
        $this->assertGreaterThanOrEqual($before, $city->getCreatedAt());
        $this->assertLessThanOrEqual($after, $city->getCreatedAt());
    }

    public function testSettersAndGetters(): void
    {
        $city = new City();

        $city
            ->setName('Berlin')
            ->setSlug('berlin')
            ->setDescription('Capital of Germany');

        $this->assertEquals('Berlin', $city->getName());
        $this->assertEquals('berlin', $city->getSlug());
        $this->assertEquals('Capital of Germany', $city->getDescription());
    }

    public function testCreatedAt(): void
    {
        $city = new City();
        $date = new \DateTime('2024-01-01');

        $city->setCreatedAt($date);

        $this->assertEquals($date, $city->getCreatedAt());
    }

    public function testAddStation(): void
    {
        $city = new City();
        $station = new Station(52.520008, 13.404954);

        $result = $city->addStation($station);

        $this->assertSame($city, $result);
        $this->assertCount(1, $city->getStations());
        $this->assertTrue($city->getStations()->contains($station));
    }

    public function testRemoveStation(): void
    {
        $city = new City();
        $station1 = new Station(52.520008, 13.404954);
        $station2 = new Station(48.137154, 11.576124);

        $city->addStation($station1);
        $city->addStation($station2);
        $city->removeStations($station1);

        $this->assertCount(1, $city->getStations());
        $this->assertFalse($city->getStations()->contains($station1));
        $this->assertTrue($city->getStations()->contains($station2));
    }

    public function testToString(): void
    {
        $city = new City();
        $city->setName('Hamburg');

        $this->assertEquals('Hamburg', (string) $city);
    }

    public function testToStringWithNullName(): void
    {
        $city = new City();

        $this->assertEquals('', (string) $city);
    }

    public function testFluentInterface(): void
    {
        $city = new City();

        $result = $city
            ->setName('Munich')
            ->setSlug('munich')
            ->setDescription('Bavaria\'s capital')
            ->setCreatedAt(new \DateTime());

        $this->assertSame($city, $result);
    }
}
