<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class UVIndexLevelColors extends AbstractLevelColors
{
    protected $backgroundColors = [
        0 => '#c0ffa0',
        1 => '#f8f040',
        2 => '#f87820',
        3 => '#a80080',
    ];

    protected $backgroundColorNames = [
        0 => 'green',
        1 => 'yellow',
        2 => 'red',
        3 => 'purple',
    ];
}