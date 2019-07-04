<?php declare(strict_types=1);

namespace App\Tests\Air\Measurement;

use App\Air\Measurement\CO;
use PHPUnit\Framework\TestCase;

class COTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('Kohlenmonoxid', (new CO())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('CO', (new CO())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('co', (new CO())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('CO', (new CO())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('µg/m<sup>3</sup>', (new CO())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('µg/m³', (new CO())->getUnitPlain());
    }
}