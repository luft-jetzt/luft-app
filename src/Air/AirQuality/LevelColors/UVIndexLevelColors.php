<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class UVIndexLevelColors extends AbstractLevelColors
{
    protected $backgroundColors = [
        0 => '#c0ffa0',
        1 => '#c0ffa0',
        2 => '#c0ffa0',
        3 => '#f8f040',
        4 => '#f8f040',
        5 => '#f8f040',
        6 => '#f87820',
        7 => '#f87820',
        8 => '#d80020',
        9 => '#d80020',
        10 => '#d80020',
        11 => '#a80080',
    ];

    protected $backgroundColorNames = [
        0 => 'white',
        1 => 'green',
        2 => 'green',
        3 => 'yellow',
        4 => 'yellow',
        5 => 'red',
        6 => 'red',
    ];
}