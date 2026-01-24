<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\LevelColors;

use App\Air\AirQuality\LevelColors\UVIndexLevelColors;
use PHPUnit\Framework\TestCase;

class UVIndexLevelColorsTest extends TestCase
{
    private UVIndexLevelColors $levelColors;

    protected function setUp(): void
    {
        $this->levelColors = new UVIndexLevelColors();
    }

    public function testGetIdentifier(): void
    {
        $this->assertEquals('uvindex', $this->levelColors->getIdentifier());
    }

    public function testGetBackgroundColors(): void
    {
        $colors = $this->levelColors->getBackgroundColors();

        $this->assertIsArray($colors);
        $this->assertNotEmpty($colors);
    }

    public function testGetBackgroundColorNames(): void
    {
        $names = $this->levelColors->getBackgroundColorNames();

        $this->assertIsArray($names);
        $this->assertNotEmpty($names);
    }

    public function testUVIndexHasFiveLevels(): void
    {
        // UV Index typically has 5 levels: low, moderate, high, very high, extreme
        $names = $this->levelColors->getBackgroundColorNames();

        // Should have at least 5 defined levels
        $this->assertGreaterThanOrEqual(5, count($names));
    }
}
