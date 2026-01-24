<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\LevelColors;

use App\Air\AirQuality\LevelColors\StandardLevelColors;
use PHPUnit\Framework\TestCase;

class StandardLevelColorsTest extends TestCase
{
    private StandardLevelColors $levelColors;

    protected function setUp(): void
    {
        $this->levelColors = new StandardLevelColors();
    }

    public function testGetIdentifier(): void
    {
        $this->assertEquals('standard', $this->levelColors->getIdentifier());
    }

    public function testGetBackgroundColors(): void
    {
        $colors = $this->levelColors->getBackgroundColors();

        $this->assertIsArray($colors);
        $this->assertCount(7, $colors);
        $this->assertEquals('white', $colors[0]);
        $this->assertEquals('#28a745', $colors[1]); // green
        $this->assertEquals('#ffc107', $colors[3]); // yellow
        $this->assertEquals('#dc3545', $colors[5]); // red
    }

    public function testGetBackgroundColorNames(): void
    {
        $names = $this->levelColors->getBackgroundColorNames();

        $this->assertIsArray($names);
        $this->assertCount(7, $names);
        $this->assertEquals('white', $names[0]);
        $this->assertEquals('green', $names[1]);
        $this->assertEquals('yellow', $names[3]);
        $this->assertEquals('red', $names[5]);
    }

    public function testColorsAndNamesHaveSameKeys(): void
    {
        $colors = $this->levelColors->getBackgroundColors();
        $names = $this->levelColors->getBackgroundColorNames();

        $this->assertEquals(array_keys($colors), array_keys($names));
    }

    public function testLevelProgression(): void
    {
        $names = $this->levelColors->getBackgroundColorNames();

        // Levels 1-2 should be green (good)
        $this->assertEquals('green', $names[1]);
        $this->assertEquals('green', $names[2]);

        // Levels 3-4 should be yellow (moderate)
        $this->assertEquals('yellow', $names[3]);
        $this->assertEquals('yellow', $names[4]);

        // Levels 5-6 should be red (bad)
        $this->assertEquals('red', $names[5]);
        $this->assertEquals('red', $names[6]);
    }
}
