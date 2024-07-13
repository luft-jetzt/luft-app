<?php declare(strict_types=1);

namespace App\Tests\Air\Pollutant;

use App\Air\Pollutant\Temperature;
use PHPUnit\Framework\TestCase;

class TemperatureTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Temperatur', (new Temperature())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('Temperature', (new Temperature())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('temperature', (new Temperature())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('Temperatur', (new Temperature())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('°C', (new Temperature())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('°C', (new Temperature())->getUnitPlain());
    }

    public function testShowOnMap(): void
    {
        $this->assertFalse((new Temperature())->showOnMap());
    }

    public function testIncludeInTweets(): void
    {
        $this->assertFalse((new Temperature())->includeInTweets());
    }
}