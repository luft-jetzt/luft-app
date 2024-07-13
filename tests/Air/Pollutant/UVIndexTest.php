<?php declare(strict_types=1);

namespace App\Tests\Air\Pollutant;

use App\Air\Pollutant\UVIndex;
use PHPUnit\Framework\TestCase;

class UVIndexTest extends TestCase
{
    public function testName(): void
    {
        $this->assertEquals('UV-Index', (new UVIndex())->getName());
    }

    public function testShortName(): void
    {
        $this->assertEquals('UVIndex', (new UVIndex())->getShortName());
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('uvindex', (new UVIndex())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertEquals('UV-Index', (new UVIndex())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertEquals('', (new UVIndex())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertEquals('', (new UVIndex())->getUnitPlain());
    }

    public function testShowOnMap(): void
    {
        $this->assertFalse((new UVIndex())->showOnMap());
    }

    public function testIncludeInTweets(): void
    {
        $this->assertFalse((new UVIndex())->includeInTweets());
    }
}