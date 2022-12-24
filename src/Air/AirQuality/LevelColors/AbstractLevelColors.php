<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

abstract class AbstractLevelColors implements LevelColorsInterface
{
    protected array $backgroundColors = [];
    protected array $backgroundColorNames = [];

    public function getBackgroundColors(): array
    {
        return $this->backgroundColors;
    }

    public function getBackgroundColorNames(): array
    {
        return $this->backgroundColorNames;
    }
}
