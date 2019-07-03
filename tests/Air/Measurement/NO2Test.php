<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\NO2;
use PHPUnit\Framework\TestCase;

class NO2Test extends TestCase
{
    public function testNO2(): void
    {
        $no2 = new NO2();

        $this->assertEquals('Stickstoffdioxid', $no2->getName());
        $this->assertEquals('NO2', $no2->getShortName());
        $this->assertEquals('no2', $no2->getIdentifier());
        $this->assertEquals('NO<sub>2</sub>', $no2->getShortNameHtml());
        $this->assertEquals('µg/m<sup>3</sup>', $no2->getUnitHtml());
        $this->assertEquals('µg/m³', $no2->getUnitPlain());
    }
}