<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColorCollection;

use App\Air\AirQuality\LevelColors\LevelColorsInterface;
use App\Air\AirQuality\LevelColors\StandardLevelColors;

class LevelColorCollection implements LevelColorCollectionInterface
{
    /** @var LevelColorsInterface $level */
    protected $level;

    public function __construct()
    {
        $this->level = new StandardLevelColors(); // todo
    }

    public function getBackgroundColor(int $pollutionLevel): string
    {
        return $this->level->getBackgroundColors()[$pollutionLevel];
    }

    public function getBackgroundColorName(int $pollutionLevel): string
    {
        return $this->level->getBackgroundColorNames()[$pollutionLevel];
    }
}
