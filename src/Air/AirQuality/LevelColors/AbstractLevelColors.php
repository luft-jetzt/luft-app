<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

abstract class AbstractLevelColors implements LevelColorsInterface
{
    protected array $backgroundColors = [];
    protected array $backgroundColorNames = [];

    #[\Override]
    public function getBackgroundColors(): array
    {
        return $this->backgroundColors;
    }

    #[\Override]
    public function getBackgroundColorNames(): array
    {
        return $this->backgroundColorNames;
    }
}
