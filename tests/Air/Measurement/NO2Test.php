<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\NO2;
use PHPUnit\Framework\TestCase;

class NO2Test extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Stickstoffdioxid', (new NO2())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('NO2', (new NO2())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('no2', (new NO2())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('NO<sub>2</sub>', (new NO2())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('µg/m<sup>3</sup>', (new NO2())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('µg/m³', (new NO2())->getUnitPlain());
    }

    public function testShowOnMap(): void
    {
        $this->assertTrue((new NO2())->showOnMap());
    }

    public function testIncludeInTweets(): void
    {
        $this->assertTrue((new NO2())->includeInTweets());
    }
}