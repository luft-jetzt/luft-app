<?php declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Data;
use App\Entity\Station;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $data = new Data();

        $dateTime = new \DateTime('2024-01-15 14:30:00');
        $data
            ->setId(123)
            ->setValue(42.5)
            ->setPollutant(1)
            ->setDateTime($dateTime)
            ->setTag('hourly');

        $this->assertEquals(123, $data->getId());
        $this->assertEquals(42.5, $data->getValue());
        $this->assertEquals(1, $data->getPollutant());
        $this->assertEquals($dateTime, $data->getDateTime());
        $this->assertEquals('hourly', $data->getTag());
    }

    public function testDateTimeFormatted(): void
    {
        $data = new Data();
        $data->setDateTime(new \DateTime('2024-06-15 10:30:45'));

        $this->assertEquals('2024-06-15 10:30:45', $data->getDateTimeFormatted());
    }

    public function testStation(): void
    {
        $station = new Station(52.520008, 13.404954);
        $station->setId(42)->setProvider('uba');

        $data = new Data();
        $data->setStation($station);

        $this->assertSame($station, $data->getStation());
        $this->assertEquals(42, $data->getStationId());
        $this->assertEquals('uba', $data->getProvider());
    }

    public function testStationIdReturnsNullWithoutStation(): void
    {
        $data = new Data();

        $this->assertNull($data->getStationId());
    }

    public function testIsIndexable(): void
    {
        $data = new Data();

        // Data from today should not be indexable (within 1 week)
        $data->setDateTime(new \DateTime());
        $this->assertFalse($data->isIndexable());

        // Data from 2 weeks ago should be indexable
        $data->setDateTime(new \DateTime('-2 weeks'));
        $this->assertTrue($data->isIndexable());
    }

    public function testFluentInterface(): void
    {
        $data = new Data();

        $result = $data
            ->setId(1)
            ->setValue(10.0)
            ->setPollutant(2)
            ->setDateTime(new \DateTime())
            ->setTag('test');

        $this->assertSame($data, $result);
    }
}
