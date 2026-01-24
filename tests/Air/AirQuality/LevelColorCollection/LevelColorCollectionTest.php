<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\LevelColorCollection;

use App\Air\AirQuality\LevelColorCollection\LevelColorCollection;
use App\Air\AirQuality\LevelColors\LevelColorsInterface;
use App\Air\AirQuality\LevelColors\StandardLevelColors;
use App\Air\AirQuality\LevelColors\UVIndexLevelColors;
use PHPUnit\Framework\TestCase;

class LevelColorCollectionTest extends TestCase
{
    public function testAddLevelColors(): void
    {
        $collection = new LevelColorCollection();

        $levelColors = $this->createMock(LevelColorsInterface::class);
        $levelColors->method('getIdentifier')->willReturn('pm10');

        $result = $collection->addLevelColors($levelColors);

        $this->assertSame($collection, $result);
        $this->assertCount(1, $collection->getLevelColorsList());
    }

    public function testGetLevelColorsForPollutant(): void
    {
        $collection = new LevelColorCollection();

        $uvColors = new UVIndexLevelColors();
        $collection->addLevelColors($uvColors);

        $result = $collection->getLevelColorsForPollutant('uvindex');

        $this->assertSame($uvColors, $result);
    }

    public function testGetLevelColorsForUnknownPollutantReturnsStandard(): void
    {
        $collection = new LevelColorCollection();

        $result = $collection->getLevelColorsForPollutant('unknown');

        $this->assertInstanceOf(StandardLevelColors::class, $result);
    }

    public function testGetLevelColorsList(): void
    {
        $collection = new LevelColorCollection();

        $colors1 = $this->createMock(LevelColorsInterface::class);
        $colors1->method('getIdentifier')->willReturn('pm10');

        $colors2 = $this->createMock(LevelColorsInterface::class);
        $colors2->method('getIdentifier')->willReturn('no2');

        $collection
            ->addLevelColors($colors1)
            ->addLevelColors($colors2);

        $list = $collection->getLevelColorsList();

        $this->assertCount(2, $list);
        $this->assertArrayHasKey('pm10', $list);
        $this->assertArrayHasKey('no2', $list);
    }

    public function testAddSameIdentifierOverwrites(): void
    {
        $collection = new LevelColorCollection();

        $colors1 = $this->createMock(LevelColorsInterface::class);
        $colors1->method('getIdentifier')->willReturn('pm10');

        $colors2 = $this->createMock(LevelColorsInterface::class);
        $colors2->method('getIdentifier')->willReturn('pm10');

        $collection
            ->addLevelColors($colors1)
            ->addLevelColors($colors2);

        $list = $collection->getLevelColorsList();

        $this->assertCount(1, $list);
        $this->assertSame($colors2, $list['pm10']);
    }
}
