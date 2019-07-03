<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\O3;
use PHPUnit\Framework\TestCase;

class O3Test extends TestCase
{
    public function testO3(): void
    {
        $o3 = new O3();

        $this->assertEquals('Ozon', $o3->getName());
        $this->assertEquals('O3', $o3->getShortName());
        $this->assertEquals('o3', $o3->getIdentifier());
        $this->assertEquals('O<sub>3</sub>', $o3->getShortNameHtml());
        $this->assertEquals('µg/m<sup>3</sup>', $o3->getUnitHtml());
        $this->assertEquals('µg/m³', $o3->getUnitPlain());
    }
}