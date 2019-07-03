<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\PM10;
use PHPUnit\Framework\TestCase;

class PM10Test extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Feinstaub PM10', (new PM10())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('PM10', (new PM10())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('pm10', (new PM10())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('PM<sub>10</sub>', (new PM10())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('µg/m<sup>3</sup>', (new PM10())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('µg/m³', (new PM10())->getUnitPlain());
    }
}