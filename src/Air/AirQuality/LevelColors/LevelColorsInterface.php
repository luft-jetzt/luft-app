<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

interface LevelColorsInterface
{
    public function getBackgroundColors(): array;
    public function getBackgroundColorNames(): array;
}
