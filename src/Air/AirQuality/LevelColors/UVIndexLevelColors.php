<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class UVIndexLevelColors extends AbstractLevelColors
{
    protected $backgroundColors = [
        1 => '#c0ffa0',
        2 => '#f8f040',
        3 => '#f87820',
        4 => '#a80080',
    ];

    protected $backgroundColorNames = [
        1 => 'green',
        2 => 'yellow',
        3 => 'red',
        4 => 'purple',
    ];
}