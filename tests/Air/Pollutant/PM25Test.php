<?php declare(strict_types=1);

namespace App\Tests\Air\Pollutant;

use App\Air\Pollutant\PM25;
use PHPUnit\Framework\TestCase;

class PM25Test extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Feinstaub PM25', (new PM25())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('PM25', (new PM25())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('pm25', (new PM25())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('PM<sub>25</sub>', (new PM25())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('µg/m<sup>3</sup>', (new PM25())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('µg/m³', (new PM25())->getUnitPlain());
    }

    public function testShowOnMap(): void
    {
        $this->assertTrue((new PM25())->showOnMap());
    }

    public function testIncludeInTweets(): void
    {
        $this->assertTrue((new PM25())->includeInTweets());
    }

    public function testDecimals(): void
    {
        $this->assertEquals(0, (new PM25())->getDecimals());
    }
}