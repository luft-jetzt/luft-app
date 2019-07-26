<?php declare(strict_types=1);

namespace App\Air\AirQuality\LevelColors;

class UVIndexLevelColors extends AbstractLevelColors
{
    protected $backgroundColors = [
        0 => '#C0FFA0',
        1 => '#C0FFA0',
        2 => '#C0FFA0',
        3 => '#F8F040',
        4 => '#F8F040',
        5 => '#F8F040',
        6 => '#F87820',
        7 => '#F87820',
        8 => '#D80020',
        9 => '#D80020',
        10 => '#D80020',
        11 => '#A80080',
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