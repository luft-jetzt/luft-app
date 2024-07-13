<?php declare(strict_types=1);

namespace App\Tests\Air\Pollutant;

use App\Air\Pollutant\CO2;
use PHPUnit\Framework\TestCase;

class CO2Test extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Kohlenstoffdioxid', (new CO2())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('CO2', (new CO2())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('co2', (new CO2())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('CO<sub>2</sub>', (new CO2())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('ppm', (new CO2())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('ppm', (new CO2())->getUnitPlain());
    }

    public function testShowOnMap(): void
    {
        $this->assertFalse((new CO2())->showOnMap());
    }

    public function testIncludeInTweets(): void
    {
        $this->assertFalse((new CO2())->includeInTweets());
    }

    public function testDecimals(): void
    {
        $this->assertEquals(2, (new CO2())->getDecimals());
    }
}