<?php declare(strict_types=1);

namespace App\Tests\Air\Pollutant;

use App\Air\Pollutant\UVIndexMax;
use PHPUnit\Framework\TestCase;

class UVIndexMaxTest extends TestCase
{
    public function testName(): void
    {
        $this->assertSame('maximaler UV-Index', (new UVIndexMax())->getName());
    }

    public function testIdentifier(): void
    {
        $this->assertSame('uvindex_max', (new UVIndexMax())->getIdentifier());
    }

    public function testShortNameHtml(): void
    {
        $this->assertSame('UV-Index Max', (new UVIndexMax())->getShortNameHtml());
    }

    public function testUnitHtml(): void
    {
        $this->assertSame('', (new UVIndexMax())->getUnitHtml());
    }

    public function testUnitPlain(): void
    {
        $this->assertSame('', (new UVIndexMax())->getUnitPlain());
    }

    public function testShowOnMap(): void
    {
        $this->assertFalse((new UVIndexMax())->showOnMap());
    }

    public function testIncludeInTweets(): void
    {
        $this->assertFalse((new UVIndexMax())->includeInTweets());
    }
}
