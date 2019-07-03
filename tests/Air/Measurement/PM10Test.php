<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\PM10;
use PHPUnit\Framework\TestCase;

class PM10Test extends TestCase
{
    public function testPM10(): void
    {
        $pm10 = new PM10();

        $this->assertEquals('Feinstaub PM10', $pm10->getName());
        $this->assertEquals('PM10', $pm10->getShortName());
        $this->assertEquals('pm10', $pm10->getIdentifier());
        $this->assertEquals('PM<sub>10</sub>', $pm10->getShortNameHtml());
        $this->assertEquals('µg/m<sup>3</sup>', $pm10->getUnitHtml());
        $this->assertEquals('µg/m³', $pm10->getUnitPlain());
    }
}