<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\SO2;
use PHPUnit\Framework\TestCase;

class SO2Test extends TestCase
{
    public function testSO2(): void
    {
        $so2 = new SO2();

        $this->assertEquals('Schwefeldioxid', $so2->getName());
        $this->assertEquals('SO2', $so2->getShortName());
        $this->assertEquals('so2', $so2->getIdentifier());
        $this->assertEquals('SO<sub>2</sub>', $so2->getShortNameHtml());
        $this->assertEquals('µg/m<sup>3</sup>', $so2->getUnitHtml());
        $this->assertEquals('µg/m³', $so2->getUnitPlain());
    }
}