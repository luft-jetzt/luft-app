<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\CO;
use PHPUnit\Framework\TestCase;

class COTest extends TestCase
{
    public function testCO(): void
    {
        $co = new CO();

        $this->assertEquals('Kohlenmonoxid', $co->getName());
        $this->assertEquals('CO', $co->getShortName());
        $this->assertEquals('co', $co->getIdentifier());
        $this->assertEquals('CO', $co->getShortNameHtml());
        $this->assertEquals('µg/m<sup>3</sup>', $co->getUnitHtml());
        $this->assertEquals('µg/m³', $co->getUnitPlain());
    }
}