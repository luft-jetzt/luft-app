<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Pollutant\SO2;
use PHPUnit\Framework\TestCase;

class SO2Test extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Schwefeldioxid', (new SO2())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('SO2', (new SO2())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('so2', (new SO2())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('SO<sub>2</sub>', (new SO2())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('µg/m<sup>3</sup>', (new SO2())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('µg/m³', (new SO2())->getUnitPlain());
    }

    public function testShowOnMap(): void
    {
        $this->assertTrue((new SO2())->showOnMap());
    }

    public function testIncludeInTweets(): void
    {
        $this->assertTrue((new SO2())->includeInTweets());
    }

    public function testDecimals(): void
    {
        $this->assertEquals(0, (new SO2())->getDecimals());
    }
}