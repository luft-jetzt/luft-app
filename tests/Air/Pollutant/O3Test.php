<?php declare(strict_types=1);

namespace App\Tests\Air\Pollutant;

use App\Air\Pollutant\O3;
use PHPUnit\Framework\TestCase;

class O3Test extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Ozon', (new O3())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('O3', (new O3())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('o3', (new O3())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('O<sub>3</sub>', (new O3())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('µg/m<sup>3</sup>', (new O3())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('µg/m³', (new O3())->getUnitPlain());
    }

    public function testShowOnMap(): void
    {
        $this->assertTrue((new O3())->showOnMap());
    }

    public function testIncludeInTweets(): void
    {
        $this->assertTrue((new O3())->includeInTweets());
    }

    public function testDecimals(): void
    {
        $this->assertEquals(0, (new O3())->getDecimals());
    }
}