<?php declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Network;
use App\Entity\Station;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class NetworkTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $network = new Network();

        $network
            ->setId(1)
            ->setName('Umweltbundesamt')
            ->setDescription('German Federal Environment Agency network')
            ->setLink('https://www.umweltbundesamt.de');

        $this->assertEquals(1, $network->getId());
        $this->assertEquals('Umweltbundesamt', $network->getName());
        $this->assertEquals('German Federal Environment Agency network', $network->getDescription());
        $this->assertEquals('https://www.umweltbundesamt.de', $network->getLink());
    }

    public function testNullValuesAreNullByDefault(): void
    {
        $network = new Network();

        $this->assertNull($network->getId());
        $this->assertNull($network->getName());
        $this->assertNull($network->getDescription());
        $this->assertNull($network->getLink());
    }

    public function testStations(): void
    {
        $network = new Network();
        $stations = new ArrayCollection([
            new Station(52.520008, 13.404954),
            new Station(48.137154, 11.576124),
        ]);

        $network->setStations($stations);

        $this->assertSame($stations, $network->getStations());
        $this->assertCount(2, $network->getStations());
    }

    public function testFluentInterface(): void
    {
        $network = new Network();

        $result = $network
            ->setId(1)
            ->setName('Test Network')
            ->setDescription('A test network')
            ->setLink('https://example.com')
            ->setStations(new ArrayCollection());

        $this->assertSame($network, $result);
    }
}
