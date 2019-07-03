<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\PM25;
use PHPUnit\Framework\TestCase;

class PM25Test extends TestCase
{
    public function testPM25(): void
    {
        $pm25 = new PM25();

        $this->assertEquals('Feinstaub PM25', $pm25->getName());
        $this->assertEquals('PM25', $pm25->getShortName());
        $this->assertEquals('pm25', $pm25->getIdentifier());
        $this->assertEquals('PM<sub>25</sub>', $pm25->getShortNameHtml());
        $this->assertEquals('µg/m<sup>3</sup>', $pm25->getUnitHtml());
        $this->assertEquals('µg/m³', $pm25->getUnitPlain());
    }
}