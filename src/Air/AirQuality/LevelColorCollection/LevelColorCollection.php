<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorCollection;

use App\Air\AirQuality\LevelColors\LevelColorsInterface;
use App\Air\AirQuality\LevelColors\StandardLevelColors;

class LevelColorCollection implements LevelColorCollectionInterface
{
    /** @var array $levelColorsList */
    protected $levelColorsList = [];

    public function __construct()
    {
        $this->level = new StandardLevelColors(); // todo
    }

    public function addLevelColors(LevelColorsInterface $levelColors): LevelColorCollectionInterface
    {
        echo get_class($levelColors);

        $this->levelColorsList[] = $levelColors;

        return $this;
    }

    public function getLevelColorsForMeasurement(string $measurementIdentifier): LevelColorsInterface
    {
        if (!array_key_exists($measurementIdentifier, $this->levelColorsList)) {
            return new StandardLevelColors();
        }

        return new StandardLevelColors();
    }
}
